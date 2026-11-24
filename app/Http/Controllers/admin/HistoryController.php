<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        return view('admin.history.index');
    }

    /**
     * JSON for DataTables
     */
    public function data()
    {
        $qs = History::with('user')  // eager‑load the user
            ->latest();  // newest first

        return datatables()
            ->eloquent($qs)
            ->addColumn('user', fn($h) => $h->user?->name ?? 'System')
            ->addColumn('event', fn($h) => ucfirst($h->event))  // Created / Updated / Deleted
            ->addColumn('entity', fn($h) => ucfirst($h->auditable_type))
            ->addColumn('when', fn($h) => format_datetime($h->created_at))
            ->addColumn('action', function ($h) {
                return '<button class="btn btn-sm btn-info view-history" data-id="' . $h->id . '">
                        <i class="fa fa-eye"></i>
                     </button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * JSON payload for the modal
     */
    public function show(History $history)
    {
        return response()->json([
            'id' => $history->id,
            'event' => $history->event,
            'key' => $history->key,
            'auditable_id' => $history->auditable_id,
            'user' => $history->user?->name ?? 'System',
            'when' => $history->created_at->toDateTimeString(),
            'new_values' => $history->new_values ?? [],
            'old_values' => $history->old_values ?? [],
        ]);
    }
}
