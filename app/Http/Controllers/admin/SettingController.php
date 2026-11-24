<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Services\CreditLimitService;
use App\Services\DatabaseBackupService;
use App\Services\DateFormatService;
use App\Services\InventoryValuationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        $users = User::query()->orderBy('name')->get(['id', 'name', 'email']);
        $overrideUserIds = app(CreditLimitService::class)->overrideUserIds();
        $valuationService = app(InventoryValuationService::class);
        $backupService = app(DatabaseBackupService::class);

        return view('admin.setting', [
            'settings' => $settings,
            'users' => $users,
            'overrideUserIds' => $overrideUserIds,
            'valuationMethodOptions' => $valuationService->methodOptions(),
            'valuationMethodEditable' => $valuationService->canChangeMethod(),
            'dateFormatOptions' => app(DateFormatService::class)->formatOptions(),
            'databaseTables' => $backupService->listTables(),
            'databaseBackups' => $backupService->listBackups(),
            'projectDatabaseName' => $backupService->projectDatabaseName(),
        ]);
    }

    public function storeSetting(Request $request)
    {
        $settings = Setting::all();
        $valuationService = app(InventoryValuationService::class);
        $rules = [];

        foreach ($settings as $setting) {
            if ($setting->key_name === CreditLimitService::SETTING_OVERRIDE_USER_IDS) {
                $rules['credit_limit_override_user_ids'] = 'nullable|array';
                $rules['credit_limit_override_user_ids.*'] = 'integer|exists:users,id';
            } elseif ($setting->key_name === InventoryValuationService::SETTING_METHOD_SET_AT) {
                continue;
            } elseif ($setting->key_name === InventoryValuationService::SETTING_METHOD) {
                if ($valuationService->canChangeMethod()) {
                    $rules[$setting->key_name] = ['required', Rule::in(array_keys($valuationService->methodOptions()))];
                }
            } elseif ($setting->key_name === InventoryValuationService::SETTING_YEAR_START_MONTH) {
                $rules[$setting->key_name] = 'required|integer|min:1|max:12';
            } elseif ($setting->key_name === DateFormatService::SETTING_KEY) {
                $rules[$setting->key_name] = ['required', Rule::in(array_keys(app(DateFormatService::class)->formatOptions()))];
            } else {
                $rules[$setting->key_name] = 'required|string|max:2000';
            }
        }

        $validated = $request->validate($rules);

        foreach ($settings as $setting) {
            if ($setting->key_name === CreditLimitService::SETTING_OVERRIDE_USER_IDS) {
                app(CreditLimitService::class)->saveOverrideUserIds(
                    $validated['credit_limit_override_user_ids'] ?? []
                );
                continue;
            }

            if ($setting->key_name === InventoryValuationService::SETTING_METHOD_SET_AT) {
                continue;
            }

            if ($setting->key_name === InventoryValuationService::SETTING_METHOD) {
                if (! $valuationService->canChangeMethod()) {
                    continue;
                }

                $newMethod = $validated[$setting->key_name];
                $setAt = trim((string) setting(InventoryValuationService::SETTING_METHOD_SET_AT, ''));

                if ($newMethod !== $setting->value || $setAt === '') {
                    $valuationService->recordMethodChange($newMethod);
                }

                continue;
            }

            $setting->value = $validated[$setting->key_name];
            $setting->save();
        }

        return back()->with('success', 'Settings updated successfully.');
    }

    public function purgeUserGeneratedData(Request $request)
    {
        $user = auth()->user();
        if (! $user || (int) $user->role_id !== 1) {
            return back()->withErrors(['purge_confirm_1' => 'Only SuperAdmin can purge data.']);
        }

        $request->validate([
            'purge_confirm_1' => ['required', 'in:CONFIRM'],
            'purge_confirm_2' => ['required', 'in:ERASE ALL DATA'],
        ], [
            'purge_confirm_1.in' => 'Type CONFIRM exactly to proceed.',
            'purge_confirm_2.in' => 'Type ERASE ALL DATA exactly to proceed.',
        ]);

        try {
            $backupFilename = app(DatabaseBackupService::class)->createPrePurgeBackup();
        } catch (\Throwable $e) {
            return back()->withErrors([
                'purge_confirm_1' => 'Pre-purge backup failed. Purge was cancelled. ' . $e->getMessage(),
            ]);
        }

        // Only tables in the configured project DB (never other MySQL databases).
        $tables = app(DatabaseBackupService::class)->listTables();
        $preservedLookup = array_fill_keys(
            array_map('strtolower', $this->preservedTableNames()),
            true
        );

        $this->disableForeignKeyChecks();
        try {
            foreach ($tables as $table) {
                $plainName = $this->plainTableName((string) $table);

                // Hard stop: never truncate exempted tables (users, org, COA, etc.).
                if (isset($preservedLookup[strtolower($plainName)])) {
                    continue;
                }

                if (! Schema::hasTable($plainName)) {
                    continue;
                }

                DB::table($plainName)->truncate();
            }
        } finally {
            $this->enableForeignKeyChecks();
        }

        return back()->with('success', 'All user-generated data has been purged from database "' . app(DatabaseBackupService::class)->projectDatabaseName() . '". Users, Organization Info, and COA were kept. Pre-purge backup: ' . $backupFilename);
    }

    public function createFullDatabaseBackup()
    {
        $this->ensureSuperAdmin();

        $filename = app(DatabaseBackupService::class)->createFullSqlBackup('full');

        return back()->with('success', 'Full database backup created: ' . $filename);
    }

    public function createTableBackup(Request $request)
    {
        $this->ensureSuperAdmin();

        $validated = $request->validate([
            'table' => ['required', 'string'],
            'format' => ['required', Rule::in(['csv', 'sql'])],
        ]);

        $service = app(DatabaseBackupService::class);
        if (! in_array($validated['table'], $service->listTables(), true)) {
            return back()->withErrors(['backup' => 'Invalid table selected.']);
        }
        $filename = $validated['format'] === 'csv'
            ? $service->createTableCsvBackup($validated['table'])
            : $service->createTableSqlBackup($validated['table']);

        return back()->with('success', 'Table backup created: ' . $filename);
    }

    public function downloadDatabaseBackup(string $filename)
    {
        $this->ensureSuperAdmin();

        return app(DatabaseBackupService::class)->downloadResponse($filename);
    }

    public function deleteDatabaseBackup(string $filename)
    {
        $this->ensureSuperAdmin();

        if (! app(DatabaseBackupService::class)->deleteBackup($filename)) {
            return back()->withErrors(['backup' => 'Backup file not found.']);
        }

        return back()->with('success', 'Backup deleted successfully.');
    }

    private function ensureSuperAdmin(): void
    {
        $user = auth()->user();
        if (! $user || (int) $user->role_id !== 1) {
            abort(403, 'Only SuperAdmin can manage database backups.');
        }
    }

    /** @return array<int, string> */
    private function preservedTableNames(): array
    {
        return [
            // Framework and auth core
            'migrations',
            'users',
            'password_reset_tokens',
            'sessions',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
            // Access control and app navigation
            'roles',
            'permissions',
            'user_permissions',
            'store_employee_permissions',
            'sidebar_links',
            // Settings and exempted masters
            'settings',
            'organization_infos',
            // Exempt COA tables
            'gl_accounts',
            'transaction_account_mappings',
            // Sales invoice types (Cash Sales, Credit Sales, Estimates)
            'invoice_options',
        ];
    }

    private function plainTableName(string $table): string
    {
        if (str_contains($table, '.')) {
            return (string) substr(strrchr($table, '.'), 1);
        }

        return $table;
    }

    private function disableForeignKeyChecks(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            return;
        }

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("SET session_replication_role = 'replica'");
        }
    }

    private function enableForeignKeyChecks(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return;
        }

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("SET session_replication_role = 'origin'");
        }
    }
}
