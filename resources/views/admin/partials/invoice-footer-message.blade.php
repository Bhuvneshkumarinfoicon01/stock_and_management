@php
    $invoiceFooterText = $organizationInfo?->invoice_footer_text
        ?? \App\Models\OrganizationInfo::DEFAULT_INVOICE_FOOTER_TEXT;
@endphp
@if($invoiceFooterText !== '')
    <p>{!! nl2br(e($invoiceFooterText)) !!}</p>
@endif
