<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Carbon as IlluminateCarbon;

class DateFormatService
{
    public const SETTING_KEY = 'date_format';

    /** @var array<string, string> Carbon format => label */
    public const FORMAT_OPTIONS = [
        'd-M-Y' => '08-Jul-2026 (dd-mmm-yyyy)',
        'd-M-y' => '08-Jul-26 (dd-mmm-yy)',
        'd M Y' => '08 Jul 2026',
        'd-m-Y' => '08-07-2026',
        'd/m/Y' => '08/07/2026',
        'Y-m-d' => '2026-07-08 (ISO)',
        'M d, Y' => 'Jul 08, 2026',
    ];

    /** @var array<string, string> Carbon format => Moment.js datetimepicker format */
    private const MOMENT_FORMATS = [
        'd-M-Y' => 'DD-MMM-YYYY',
        'd-M-y' => 'DD-MMM-YY',
        'd M Y' => 'DD MMM YYYY',
        'd-m-Y' => 'DD-MM-YYYY',
        'd/m/Y' => 'DD/MM/YYYY',
        'Y-m-d' => 'YYYY-MM-DD',
        'M d, Y' => 'MMM DD, YYYY',
    ];

    /** @return array<string, string> */
    public function formatOptions(): array
    {
        return self::FORMAT_OPTIONS;
    }

    public function defaultFormat(): string
    {
        return 'd-M-Y';
    }

    public function currentFormat(): string
    {
        $format = (string) setting(self::SETTING_KEY, $this->defaultFormat());

        return array_key_exists($format, self::FORMAT_OPTIONS) ? $format : $this->defaultFormat();
    }

    public function momentFormat(): string
    {
        $carbon = $this->currentFormat();

        return self::MOMENT_FORMATS[$carbon] ?? 'DD-MMM-YYYY';
    }

    /** @return array<string, mixed> */
    public function config(): array
    {
        $format = $this->currentFormat();

        return [
            'format' => $format,
            'momentFormat' => $this->momentFormat(),
            'example' => Carbon::now()->format($format),
            'options' => $this->formatOptions(),
        ];
    }

    public function format(mixed $date, string $empty = '—'): string
    {
        if ($date === null || $date === '') {
            return $empty;
        }

        try {
            $carbon = $date instanceof Carbon || $date instanceof IlluminateCarbon
                ? $date
                : Carbon::parse($date);
        } catch (\Throwable) {
            return $empty;
        }

        // Old hard-coded display:
        // return $carbon->format('d M Y');

        return $carbon->format($this->currentFormat());
    }

    public function formatDateTime(mixed $date, string $empty = '—'): string
    {
        if ($date === null || $date === '') {
            return $empty;
        }

        try {
            $carbon = $date instanceof Carbon || $date instanceof IlluminateCarbon
                ? $date
                : Carbon::parse($date);
        } catch (\Throwable) {
            return $empty;
        }

        // Old hard-coded display:
        // return $carbon->format('d M Y H:i');

        return $carbon->format($this->currentFormat() . ' H:i');
    }

    public function formatDateTimeAmPm(mixed $date, string $empty = '—'): string
    {
        if ($date === null || $date === '') {
            return $empty;
        }

        try {
            $carbon = $date instanceof Carbon || $date instanceof IlluminateCarbon
                ? $date
                : Carbon::parse($date);
        } catch (\Throwable) {
            return $empty;
        }

        // Old hard-coded display:
        // return $carbon->format('d M Y h:i A');

        return $carbon->format($this->currentFormat() . ' h:i A');
    }

    public function parseInput(?string $value): ?Carbon
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $formats = array_values(array_unique(array_merge(
            [$this->currentFormat()],
            array_keys(self::FORMAT_OPTIONS),
            ['d-m-Y', 'Y-m-d']
        )));

        foreach ($formats as $format) {
            try {
                if (! Carbon::hasFormat($value, $format)) {
                    continue;
                }

                return Carbon::createFromFormat($format, $value)->startOfDay();
            } catch (\Throwable) {
                continue;
            }
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    public function parseInputToDateString(?string $value): ?string
    {
        return $this->parseInput($value)?->format('Y-m-d');
    }
}
