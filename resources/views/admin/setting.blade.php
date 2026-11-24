@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')>

<div class="page-wrapper">
    <div class="content settings-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="settings-wrapper d-flex">
                    <div class="settings-page-wrap">
                        <div class="setting-title">
                            <h4>Settings</h4>
                        </div>
                        <div class="security-settings">


                            <form action="{{route('admin.setting-store')}}" method="POST">
                                @csrf
                                <div class="row">
                                    @foreach($settings as $setting)
                                    @if($setting->key_name === \App\Services\CreditLimitService::SETTING_OVERRIDE_USER_IDS)
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label">{{ $setting->label }}</label>
                                            <p class="text-muted small">{{ $setting->placeholder }}</p>
                                            <select name="credit_limit_override_user_ids[]" class="form-select" multiple size="8">
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" @selected(in_array($user->id, $overrideUserIds, true))>
                                                        {{ $user->name }} ({{ $user->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <p class="small text-muted mb-0">Hold Ctrl/Cmd to select multiple users. These users may confirm or cancel invoices held because a credit limit was exceeded.</p>
                                            @error('credit_limit_override_user_ids')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @elseif($setting->key_name === \App\Services\InventoryValuationService::SETTING_METHOD_SET_AT)
                                    @continue
                                    @elseif($setting->key_name === \App\Services\InventoryValuationService::SETTING_METHOD)
                                    <div class="col-lg-4 col-sm-6 col-12">
                                        <div class="mb-3">
                                            <label class="form-label">{{ $setting->label }}</label>
                                            @if($valuationMethodEditable)
                                                <select name="{{ $setting->key_name }}" class="form-select @error($setting->key_name) is-invalid @enderror">
                                                    @foreach($valuationMethodOptions as $value => $label)
                                                        <option value="{{ $value }}" @selected(old($setting->key_name, $setting->value) === $value)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                <p class="small text-muted mb-0">Can be changed during initial setup or within the first 30 days after the accounting year start month.</p>
                                            @else
                                                <input type="text" class="form-control" readonly
                                                    value="{{ $valuationMethodOptions[$setting->value] ?? $setting->value }}">
                                                <input type="hidden" name="{{ $setting->key_name }}" value="{{ $setting->value }}">
                                                <p class="small text-muted mb-0">Locked outside the initial setup and accounting-year start window. Save a new accounting year start month first if you need to change the method before the next natural period.</p>
                                            @endif
                                            @error($setting->key_name)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @elseif($setting->key_name === \App\Services\DateFormatService::SETTING_KEY)
                                    <div class="col-lg-4 col-sm-6 col-12">
                                        <div class="mb-3">
                                            <label class="form-label">{{ $setting->label }}</label>
                                            <select name="{{ $setting->key_name }}" class="form-select @error($setting->key_name) is-invalid @enderror">
                                                @foreach($dateFormatOptions as $value => $label)
                                                    <option value="{{ $value }}" @selected(old($setting->key_name, $setting->value) === $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <p class="small text-muted mb-0">Used for dates in lists, documents, reports, and date pickers. Example today: {{ \Carbon\Carbon::now()->format(old($setting->key_name, $setting->value) ?: 'd-M-Y') }}</p>
                                            @error($setting->key_name)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @elseif($setting->key_name === \App\Services\InventoryValuationService::SETTING_YEAR_START_MONTH)
                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <div class="mb-3">
                                            <label class="form-label">{{ $setting->label }}</label>
                                            <input type="number" min="1" max="12" step="1"
                                                name="{{ $setting->key_name }}"
                                                class="form-control @error($setting->key_name) is-invalid @enderror"
                                                value="{{ old($setting->key_name, $setting->value ?? 1) }}">
                                            <p class="small text-muted mb-0">Month (1–12) when each accounting year begins. Used with the valuation method change window above.</p>
                                            @error($setting->key_name)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <div class="mb-3">
                                            <label class="form-label">{{$setting->label}}</label>
                                            <input type="text" placeholder="{{$setting->placeholder}}"
                                                name="{{$setting->key_name}}"
                                                class="form-control @error($setting->key_name) is-invalid @enderror"
                                                value="{{ old($setting->key_name, $setting->value ?? '') }}">
                                            @error($setting->key_name)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @endif
                                    @endforeach

                                </div>

                                <div class="text-end settings-bottom-btn">
                                    <a href="javascript:void(0);" class="btn btn-cancel me-2">Reset</a>
                                    <button type="submit" class="btn btn-submit">Save Changes</button>
                                </div>
                            </form>

                            @if (auth()->user() && (int) auth()->user()->role_id === 1)
                            <hr class="my-4">
                            <div class="card border-primary mb-4">
                                <div class="card-body">
                                    <h5 class="text-primary mb-2">Database Backup</h5>
                                    <p class="mb-3">
                                        Create full database backups or export a single table as CSV or SQL
                                        from project database <strong>{{ $projectDatabaseName }}</strong> only.
                                        Backups are stored on the server and can be downloaded from here.
                                    </p>

                                    <form action="{{ route('admin.setting-backup-full') }}" method="POST" class="d-inline-block mb-3">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">
                                            Backup Full Database (.sql)
                                        </button>
                                    </form>

                                    {{--<form action="{{ route('admin.setting-backup-table') }}" method="POST" class="row g-2 align-items-end mb-4">
                                        @csrf
                                        <div class="col-md-5">
                                            <label class="form-label mb-1" for="backup_table">Table</label>
                                            <select name="table" id="backup_table" class="form-select" required>
                                                <option value="">Choose table</option>
                                                @foreach($databaseTables as $tableName)
                                                    <option value="{{ $tableName }}">{{ $tableName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label mb-1" for="backup_format">Format</label>
                                            <select name="format" id="backup_format" class="form-select" required>
                                                <option value="sql">SQL (.sql)</option>
                                                <option value="csv">CSV (.csv)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-outline-primary w-100">
                                                Backup Selected Table
                                            </button>
                                        </div>
                                    </form> --}}

                                    @error('backup')
                                    <div class="alert alert-danger py-2">{{ $message }}</div>
                                    @enderror

                                    <h6 class="mb-2">Saved Backups</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>File</th>
                                                    <th>Size</th>
                                                    <th>Created</th>
                                                    <th class="text-end">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($databaseBackups as $backup)
                                                <tr>
                                                    <td>{{ $backup['name'] }}</td>
                                                    <td>{{ $backup['size_label'] }}</td>
                                                    <td>{{ $backup['created_at'] }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('admin.setting-backup-download', $backup['name']) }}" class="btn btn-sm btn-outline-primary">
                                                            Download
                                                        </a>
                                                        <form action="{{ route('admin.setting-backup-delete', $backup['name']) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this backup file?')">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-muted">No backups created yet.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-danger">
                                <div class="card-body">
                                    <h5 class="text-danger mb-2">Danger Zone</h5>
                                    <p class="mb-3">
                                        Purge all user-generated data from project database
                                        <strong>{{ $projectDatabaseName }}</strong> only
                                        (sales, purchases, GRNs, products, ledgers, and related transactions).
                                        Other MySQL databases are never touched. Users, Organization Info, and COA are kept.
                                    </p>
                                    @error('purge_confirm_1')
                                    <div class="alert alert-danger py-2">{{ $message }}</div>
                                    @enderror
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#purgeDataModal">
                                        Purge All Data
                                    </button>
                                </div>
                            </div>
                            @endif


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')

@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: "{{ Js::from(session('success')) }}",
        timer: 2000,
        showConfirmButton: false
    });
</script>
@endif

@if (auth()->user() && (int) auth()->user()->role_id === 1)
<div class="modal fade" id="purgeDataModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.setting-purge-data') }}" id="purge-data-form">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Confirm Data Purge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="purge-modal-close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">
                        Are you sure you want to erase ALL data from the database?
                    </p>
                    <p class="fw-semibold mb-3">
                        Type <code>CONFIRM</code> first, and then type <code>ERASE ALL DATA</code> to proceed.
                    </p>
                                    <p class="small text-muted mb-3">
                                        Users, Organization Info, and COA will be kept. A full SQL backup is created automatically before purge starts.
                                    </p>

                    <label class="form-label fw-semibold mb-1" for="purge_confirm_1">CONFIRM</label>
                    <input id="purge_confirm_1" type="text" name="purge_confirm_1" class="form-control mb-2" placeholder="Type CONFIRM" required autocomplete="off">

                    <label class="form-label fw-semibold mb-1" for="purge_confirm_2">ERASE ALL DATA</label>
                    <input id="purge_confirm_2" type="text" name="purge_confirm_2" class="form-control" placeholder="Type ERASE ALL DATA" required autocomplete="off">

                    @error('purge_confirm_1')
                        <div class="alert alert-danger py-2 mt-2">{{ $message }}</div>
                    @enderror
                    @error('purge_confirm_2')
                        <div class="alert alert-danger py-2 mt-2">{{ $message }}</div>
                    @enderror

                    <div id="purge-progress" class="alert alert-warning mt-3 d-none mb-0">
                        Purging in progress... Please wait. Do not close or refresh this page.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="purge-cancel-btn">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="purge-submit-btn">Purge Database</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="purge-overlay" class="d-none" style="position:fixed;inset:0;z-index:2000;background:rgba(15,23,42,.55);display:flex;align-items:center;justify-content:center;">
    <div class="bg-white rounded p-4 text-center shadow" style="max-width:420px;">
        <div class="spinner-border text-danger mb-3" role="status"></div>
        <h5 class="mb-2">Purging database...</h5>
        <p class="mb-0 text-muted">Please wait. The screen is locked until the purge finishes.</p>
    </div>
</div>

<script>
(function () {
    const form = document.getElementById('purge-data-form');
    if (!form) {
        return;
    }

    form.addEventListener('submit', function () {
        const submitBtn = document.getElementById('purge-submit-btn');
        const cancelBtn = document.getElementById('purge-cancel-btn');
        const closeBtn = document.getElementById('purge-modal-close');
        const progress = document.getElementById('purge-progress');
        const overlay = document.getElementById('purge-overlay');

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerText = 'Purging...';
        }
        if (cancelBtn) {
            cancelBtn.disabled = true;
        }
        if (closeBtn) {
            closeBtn.disabled = true;
        }
        if (progress) {
            progress.classList.remove('d-none');
        }
        if (overlay) {
            overlay.classList.remove('d-none');
            overlay.style.display = 'flex';
        }

        document.body.style.pointerEvents = 'none';
        if (overlay) {
            overlay.style.pointerEvents = 'auto';
        }
    });
})();
</script>
@endif
@endsection
