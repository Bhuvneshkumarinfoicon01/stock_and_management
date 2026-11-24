<?php

use App\Services\DateFormatService;
use Carbon\Carbon;

if (! function_exists('format_date')) {
    function format_date(mixed $date, string $empty = '—'): string
    {
        return app(DateFormatService::class)->format($date, $empty);
    }
}

if (! function_exists('format_datetime')) {
    function format_datetime(mixed $date, string $empty = '—'): string
    {
        return app(DateFormatService::class)->formatDateTime($date, $empty);
    }
}

if (! function_exists('format_datetime_ampm')) {
    function format_datetime_ampm(mixed $date, string $empty = '—'): string
    {
        return app(DateFormatService::class)->formatDateTimeAmPm($date, $empty);
    }
}

if (! function_exists('parse_date_input')) {
    function parse_date_input(?string $value): ?Carbon
    {
        return app(DateFormatService::class)->parseInput($value);
    }
}

if (! function_exists('parse_date_input_to_db')) {
    function parse_date_input_to_db(?string $value): ?string
    {
        return app(DateFormatService::class)->parseInputToDateString($value);
    }
}

if (! function_exists('date_format_config')) {
    /** @return array<string, mixed> */
    function date_format_config(): array
    {
        return app(DateFormatService::class)->config();
    }
}

if (! function_exists('date_picker_moment_format')) {
    function date_picker_moment_format(): string
    {
        return app(DateFormatService::class)->momentFormat();
    }
}
