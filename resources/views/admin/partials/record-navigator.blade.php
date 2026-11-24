@php
    $recordNav = $recordNav ?? null;
@endphp
@if(!empty($recordNav) && ($recordNav['total'] ?? 0) > 1 && ($recordNav['position'] ?? 0) > 0)
    <div class="d-inline-flex align-items-center gap-1 record-nav"
         data-record-nav
         data-prev="{{ $recordNav['previous'] ?? '' }}"
         data-next="{{ $recordNav['next'] ?? '' }}">
        @if(!empty($recordNav['previous']))
            <a href="{{ $recordNav['previous'] }}" class="btn btn-outline-secondary" title="Previous record">
                <i data-feather="chevron-left" class="feather-16"></i><span class="d-none d-sm-inline ms-1">Previous</span>
            </a>
        @else
            <button type="button" class="btn btn-outline-secondary" disabled title="Previous record">
                <i data-feather="chevron-left" class="feather-16"></i><span class="d-none d-sm-inline ms-1">Previous</span>
            </button>
        @endif
        <span class="text-muted small px-1">{{ $recordNav['position'] }} / {{ $recordNav['total'] }}</span>
        @if(!empty($recordNav['next']))
            <a href="{{ $recordNav['next'] }}" class="btn btn-outline-secondary" title="Next record">
                <span class="d-none d-sm-inline me-1">Next</span><i data-feather="chevron-right" class="feather-16"></i>
            </a>
        @else
            <button type="button" class="btn btn-outline-secondary" disabled title="Next record">
                <span class="d-none d-sm-inline me-1">Next</span><i data-feather="chevron-right" class="feather-16"></i>
            </button>
        @endif
    </div>
    @once
        <script>
            document.addEventListener('keydown', function (event) {
                if (!event.altKey || (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight')) {
                    return;
                }
                var target = event.target;
                if (target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.tagName === 'SELECT' || target.isContentEditable)) {
                    return;
                }
                var nav = document.querySelector('[data-record-nav]');
                if (!nav) {
                    return;
                }
                var url = event.key === 'ArrowLeft' ? nav.getAttribute('data-prev') : nav.getAttribute('data-next');
                if (url) {
                    event.preventDefault();
                    window.location.href = url;
                }
            });
        </script>
    @endonce
@endif
