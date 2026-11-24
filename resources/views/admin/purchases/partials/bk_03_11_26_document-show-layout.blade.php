@php
    $title = $title ?? 'Document';
    $subtitle = $subtitle ?? '';
    $backUrl = $backUrl ?? url()->previous();
    $headerActions = $headerActions ?? [];
    
@endphp

<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap gap-2">
            <div class="page-title">
                <h4>{{ $title }}</h4>
                @if($subtitle !== '')
                    <h6>{{ $subtitle }}</h6>
                @endif
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                @foreach($headerActions as $action)
                    <a href="{{ $action['url'] }}" class="btn {{ $action['class'] ?? 'btn-outline-primary' }}">{{ $action['label'] }}</a>
                @endforeach
                <a href="{{ $backUrl }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{ $slot }}
    </div>
</div>
