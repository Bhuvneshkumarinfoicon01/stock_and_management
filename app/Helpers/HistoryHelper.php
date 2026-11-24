<?php
use App\Models\History;
use Carbon\Carbon;

if (!function_exists('storeHistory')) {
    /**
     * Persist an audit‑trail entry.
     */
    function storeHistory(
        string $formKey,
        string $event,
        string $entityType,
        int $entityId,
        ?array $newValues = null,
        ?array $oldValues = null
    ): void {
        History::forceCreate([
            'user_id' => auth()->id(),
            'key' => $formKey,
            'event' => $event,
            'auditable_type' => $entityType,
            'auditable_id' => $entityId,
            'requested_data' => request()->all(),
            'new_values' => $newValues,
            'old_values' => $oldValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'url' => request()->fullUrl(),
            'created_at' => Carbon::now(),
        ]);
    }
}
