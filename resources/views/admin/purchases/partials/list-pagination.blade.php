@if($paginator->hasPages() || $paginator->total() > 0)
    @php
        $showPerPage = $showPerPage ?? true;
        $showLinks = $showLinks ?? true;
        $showSummary = $showSummary ?? true;
        $containerClass = $containerClass ?? 'mt-3';
        $lengthOptions = $datatableConfig['lengthOptions'] ?? [10, 25, 50, 100];
        $perPageParam = request('per_page');
        $selectedPerPage = is_numeric($perPageParam) ? (int) $perPageParam : (int) $paginator->perPage();
        $isAllSelected = $perPageParam === 'all';
        $isCustomSelected = !$isAllSelected && $selectedPerPage > 0 && !in_array($selectedPerPage, $lengthOptions, true);
        $customValue = $isCustomSelected ? $selectedPerPage : '';
        $formClass = 'js-list-per-page-form-' . substr(md5(url()->current() . '-' . spl_object_id($paginator)), 0, 8);
    @endphp
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 {{ $containerClass }}">
        <div class="d-flex flex-wrap align-items-center gap-3">
            @if($showSummary)
                <p class="text-muted small mb-0">
                    Showing {{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }}
                </p>
            @endif
            @if($showPerPage)
                <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center gap-2 {{ $formClass }}">
                    @foreach(request()->except(['per_page', 'page']) as $key => $value)
                        @if(is_array($value))
                            @foreach($value as $arrValue)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $arrValue }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <label class="small text-muted mb-0">Rows</label>
                    <select name="per_page" class="form-select form-select-sm js-list-per-page-select">
                        @foreach($lengthOptions as $option)
                            <option value="{{ $option }}" @selected($selectedPerPage === (int) $option)>{{ $option }}</option>
                        @endforeach
                        <option value="custom" @selected($isCustomSelected)>Custom...</option>
                        <option value="all" @selected($isAllSelected)>All</option>
                    </select>
                    <input
                        type="number"
                        name="per_page_custom"
                        min="1"
                        max="10000"
                        value="{{ $customValue }}"
                        class="form-control form-control-sm js-list-per-page-custom"
                        style="width: 6.5rem; {{ $isCustomSelected ? '' : 'display:none;' }}"
                        placeholder="Rows">
                    <button type="submit" class="btn btn-sm btn-outline-primary js-list-per-page-apply" style="{{ $isCustomSelected ? '' : 'display:none;' }}">Apply</button>
                </form>
            @endif
        </div>
        @if($showLinks)
            <div class="mb-0">
                {{ $paginator->links() }}
            </div>
        @endif
    </div>
    @if($showPerPage)
        <script>
            (function () {
                const form = document.querySelector('.{{ $formClass }}');
                if (!form) {
                    return;
                }

                const select = form.querySelector('.js-list-per-page-select');
                const customInput = form.querySelector('.js-list-per-page-custom');
                const applyBtn = form.querySelector('.js-list-per-page-apply');

                function toggleCustomMode() {
                    const customMode = select.value === 'custom';
                    customInput.style.display = customMode ? '' : 'none';
                    applyBtn.style.display = customMode ? '' : 'none';
                    customInput.disabled = !customMode;
                    applyBtn.disabled = !customMode;
                }

                toggleCustomMode();

                select.addEventListener('change', function () {
                    if (select.value === 'custom') {
                        toggleCustomMode();
                        customInput.focus();
                        return;
                    }

                    toggleCustomMode();
                    form.submit();
                });

                form.addEventListener('submit', function (e) {
                    if (select.value !== 'custom') {
                        return;
                    }

                    const custom = parseInt(customInput.value || '0', 10);
                    if (!Number.isInteger(custom) || custom < 1 || custom > 10000) {
                        e.preventDefault();
                        customInput.focus();
                        return;
                    }

                    const perPageHidden = document.createElement('input');
                    perPageHidden.type = 'hidden';
                    perPageHidden.name = 'per_page';
                    perPageHidden.value = String(custom);
                    form.appendChild(perPageHidden);
                });
            })();
        </script>
    @endif
@endif
