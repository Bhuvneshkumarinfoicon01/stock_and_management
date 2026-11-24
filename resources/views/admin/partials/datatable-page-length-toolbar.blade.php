@php
    $toolbarId = $toolbarId ?? ($tableKey . '-dt-toolbar');
    $lengthOptions = $datatableConfig['lengthOptions'] ?? [10, 25, 50, 100];
@endphp
<div id="{{ $toolbarId }}" class="datatable-page-length-toolbar d-flex flex-wrap align-items-center gap-2"
    data-table-key="{{ $tableKey }}"
    data-default-length="{{ $datatableConfig['defaultLength'] ?? 25 }}"
    data-scroll-chunk="{{ $datatableConfig['scrollChunkSize'] ?? 50 }}"
    data-length-options="{{ implode(',', $lengthOptions) }}">
    <label class="form-label mb-0 me-1" for="{{ $toolbarId }}-select">Show</label>
    <select id="{{ $toolbarId }}-select" class="form-select form-select-sm dt-page-length-select" style="width: auto; min-width: 7rem;">
        @foreach ($lengthOptions as $option)
            <option value="{{ $option }}">{{ $option }} rows</option>
        @endforeach
        <option value="custom">Custom...</option>
        <option value="all">All</option>
    </select>
    <input type="number"
        id="{{ $toolbarId }}-custom"
        class="form-control form-control-sm dt-page-length-custom"
        style="width: 6.5rem; display: none;"
        min="1"
        max="10000"
        placeholder="Rows">
    <button type="button" class="btn btn-sm btn-outline-primary dt-page-length-apply" style="display: none;">Apply</button>
    <span class="text-muted small dt-scroll-status"></span>
</div>
