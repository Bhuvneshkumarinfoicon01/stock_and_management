<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseBackupService
{
    public const BACKUP_DIR = 'database-backups';

    /**
     * Tables from the configured project database only (never other MySQL schemas).
     *
     * @return array<int, string>
     */
    public function listTables(): array
    {
        $database = $this->projectDatabaseName();
        if ($database === '') {
            return [];
        }

        // Passing null lists every non-system schema the MySQL user can see.
        $tables = Schema::getTableListing($database, false);

        $names = array_values(array_unique(array_filter(array_map(
            fn ($table) => $this->plainTableName((string) $table),
            $tables
        ))));

        sort($names);

        return $names;
    }

    public function projectDatabaseName(): string
    {
        $connection = (string) config('database.default', 'mysql');

        return (string) (
            DB::connection()->getDatabaseName()
            ?: config("database.connections.{$connection}.database")
            ?: ''
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function listBackups(): array
    {
        $dir = $this->backupDirectory();

        if (! File::isDirectory($dir)) {
            return [];
        }

        $files = collect(File::files($dir))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(function ($file) {
                return [
                    'name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'size_label' => $this->formatBytes($file->getSize()),
                    'created_at' => date('d-M-Y H:i:s', $file->getMTime()),
                ];
            })
            ->values()
            ->all();

        return $files;
    }

    public function createFullSqlBackup(string $prefix = 'full'): string
    {
        $filename = $this->buildFilename($prefix, 'sql');
        $path = $this->backupDirectory() . DIRECTORY_SEPARATOR . $filename;

        $dump = $this->tryMysqldump();
        if ($dump === null) {
            $dump = $this->buildSqlDump($this->listTables());
        }

        File::ensureDirectoryExists($this->backupDirectory());
        File::put($path, $dump);

        return $filename;
    }

    public function createPrePurgeBackup(): string
    {
        return $this->createFullSqlBackup('pre-purge');
    }

    public function createTableSqlBackup(string $table): string
    {
        $table = $this->plainTableName($table);
        $this->assertTableExists($table);

        $filename = $this->buildFilename('table_' . $table, 'sql');
        $path = $this->backupDirectory() . DIRECTORY_SEPARATOR . $filename;

        File::ensureDirectoryExists($this->backupDirectory());
        File::put($path, $this->buildSqlDump([$table]));

        return $filename;
    }

    public function createTableCsvBackup(string $table): string
    {
        $table = $this->plainTableName($table);
        $this->assertTableExists($table);

        $filename = $this->buildFilename('table_' . $table, 'csv');
        $path = $this->backupDirectory() . DIRECTORY_SEPARATOR . $filename;

        File::ensureDirectoryExists($this->backupDirectory());

        $handle = fopen($path, 'w');
        if ($handle === false) {
            throw new \RuntimeException('Unable to create backup file.');
        }

        $rows = DB::table($table)->get();
        if ($rows->isNotEmpty()) {
            $first = (array) $rows->first();
            fputcsv($handle, array_keys($first));

            foreach ($rows as $row) {
                fputcsv($handle, array_values((array) $row));
            }
        }

        fclose($handle);

        return $filename;
    }

    public function resolveBackupPath(string $filename): ?string
    {
        $filename = basename($filename);
        $path = $this->backupDirectory() . DIRECTORY_SEPARATOR . $filename;

        if (! File::exists($path)) {
            return null;
        }

        return $path;
    }

    public function deleteBackup(string $filename): bool
    {
        $path = $this->resolveBackupPath($filename);
        if (! $path) {
            return false;
        }

        return File::delete($path);
    }

    public function downloadResponse(string $filename): StreamedResponse
    {
        $path = $this->resolveBackupPath($filename);
        if (! $path) {
            abort(404, 'Backup file not found.');
        }

        $mime = str_ends_with(strtolower($filename), '.csv')
            ? 'text/csv'
            : 'application/sql';

        return response()->streamDownload(function () use ($path) {
            $stream = fopen($path, 'rb');
            if ($stream !== false) {
                fpassthru($stream);
                fclose($stream);
            }
        }, $filename, [
            'Content-Type' => $mime,
        ]);
    }

    /** @param array<int, string> $tables */
    private function buildSqlDump(array $tables): string
    {
        $allowed = array_fill_keys($this->listTables(), true);
        $lines = [];
        $database = $this->projectDatabaseName();
        $lines[] = '-- Database backup generated at ' . now()->toDateTimeString();
        $lines[] = '-- Project database: ' . $database;
        $lines[] = 'SET FOREIGN_KEY_CHECKS=0;';
        $lines[] = '';

        foreach ($tables as $table) {
            $table = $this->plainTableName($table);
            if (! isset($allowed[$table])) {
                continue;
            }

            $lines[] = '-- Table: ' . $table;
            $lines[] = 'TRUNCATE TABLE `' . $table . '`;';

            $rows = DB::table($table)->get();
            foreach ($rows as $row) {
                $data = (array) $row;
                $columns = array_map(fn ($col) => '`' . str_replace('`', '``', $col) . '`', array_keys($data));
                $values = array_map(fn ($value) => $this->sqlValue($value), array_values($data));

                $lines[] = 'INSERT INTO `' . $table . '` (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ');';
            }

            $lines[] = '';
        }

        $lines[] = 'SET FOREIGN_KEY_CHECKS=1;';

        return implode(PHP_EOL, $lines);
    }

    private function tryMysqldump(): ?string
    {
        if (DB::getDriverName() !== 'mysql') {
            return null;
        }

        $binary = trim((string) shell_exec('command -v mysqldump 2>/dev/null'));
        if ($binary === '') {
            return null;
        }

        $database = (string) config('database.connections.mysql.database');
        $host = (string) config('database.connections.mysql.host');
        $port = (string) config('database.connections.mysql.port', '3306');
        $username = (string) config('database.connections.mysql.username');
        $password = (string) config('database.connections.mysql.password');

        $command = sprintf(
            '%s --host=%s --port=%s --user=%s --single-transaction --routines --triggers %s 2>/dev/null',
            escapeshellarg($binary),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database)
        );

        if ($password !== '') {
            $command = sprintf(
                'MYSQL_PWD=%s %s',
                escapeshellarg($password),
                $command
            );
        }

        $output = shell_exec($command);
        if (! is_string($output) || trim($output) === '') {
            return null;
        }

        return $output;
    }

    private function sqlValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return DB::getPdo()->quote((string) $value);
    }

    private function backupDirectory(): string
    {
        return storage_path('app/' . self::BACKUP_DIR);
    }

    private function buildFilename(string $prefix, string $extension): string
    {
        $safePrefix = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $prefix) ?: 'backup';

        return $safePrefix . '_' . now()->format('Ymd_His') . '.' . $extension;
    }

    private function plainTableName(string $table): string
    {
        if (str_contains($table, '.')) {
            return (string) substr(strrchr($table, '.'), 1);
        }

        return $table;
    }

    private function assertTableExists(string $table): void
    {
        $table = $this->plainTableName($table);

        if (! in_array($table, $this->listTables(), true)) {
            throw new \InvalidArgumentException('Table not found in project database: ' . $table);
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        if ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        }

        return round($bytes / 1048576, 2) . ' MB';
    }
}
