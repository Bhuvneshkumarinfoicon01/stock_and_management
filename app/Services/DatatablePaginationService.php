<?php

namespace App\Services;

class DatatablePaginationService
{
    public const SETTING_OPTIONS = 'datatable_page_length_options';

    public const SETTING_DEFAULT = 'datatable_default_page_length';

    public const SETTING_SCROLL_CHUNK = 'datatable_scroll_chunk_size';

    /** @return array<int, int> */
    public function lengthOptions(): array
    {
        return $this->parseLengthOptions((string) setting(self::SETTING_OPTIONS, '10,25,50,100,250,500'));
    }

    /** @return array<int, int> */
    public function parseLengthOptions(string $raw): array
    {
        $values = array_values(array_unique(array_filter(
            array_map(static fn ($part) => (int) trim($part), explode(',', $raw)),
            static fn (int $value) => $value > 0
        )));

        if ($values === []) {
            return [10, 25, 50, 100];
        }

        sort($values);

        return $values;
    }

    public function defaultLength(): int
    {
        $default = (int) setting(self::SETTING_DEFAULT, 25);
        $options = $this->lengthOptions();

        if ($default > 0 && in_array($default, $options, true)) {
            return $default;
        }

        return $options[0] ?? 25;
    }

    public function scrollChunkSize(): int
    {
        return max(10, (int) setting(self::SETTING_SCROLL_CHUNK, 50));
    }

    /** @return array<string, mixed> */
    public function config(?string $tableKey = null, array $overrides = []): array
    {
        return array_merge([
            'tableKey' => $tableKey ?? 'default',
            'lengthOptions' => $this->lengthOptions(),
            'defaultLength' => $this->defaultLength(),
            'scrollChunkSize' => $this->scrollChunkSize(),
            'fixedHeader' => true,
            'scrollY' => 'calc(100vh - 300px)',
        ], $overrides);
    }
}
