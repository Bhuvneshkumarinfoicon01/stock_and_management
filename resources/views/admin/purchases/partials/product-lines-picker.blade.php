{{-- Product search + line table. Pass: pickerPrefix, storeSelectId, priceColumnLabel (optional), perLineTax (optional) --}}
@php
    $pickerPrefix = $pickerPrefix ?? 'pdoc';
    $priceColumnLabel = $priceColumnLabel ?? 'Unit Price';
    $perLineTax = !empty($perLineTax);
@endphp
@if($perLineTax)
<div class="col-12 mb-2">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="tax_exempt" id="{{ $pickerPrefix }}_tax_exempt" value="1"
            @checked(old('tax_exempt', $taxExemptDefault ?? false))>
        <label class="form-check-label" for="{{ $pickerPrefix }}_tax_exempt">
            Entire invoice is non-taxable (tax-exempt)
        </label>
    </div>
    <small class="text-muted d-block mb-3">When checked, no line tax is applied regardless of product settings</small>
    {{--<small class="text-muted d-block mb-3">When checked, no line tax is applied regardless of product settings. Unchecking restores each line to the product's default tax status.</small>--}}
</div>
@endif
<div class="col-lg-12 mb-3">
    <label class="form-label">Product</label>
    <div class="input-blocks search-form mb-3 position-relative">
        <input type="text"
            id="{{ $pickerPrefix }}_product_search"
            class="form-control {{ $pickerPrefix }}-product-search"
            autocomplete="off"
            placeholder="Search Product by Name or SKU" />
        <ul id="{{ $pickerPrefix }}_product_list"
            class="list-group {{ $pickerPrefix }}-product-list mt-1"
            style="max-height: 220px; overflow-y: auto; position: absolute; z-index: 1050; width: 100%; display: none;"></ul>
        <i data-feather="search" class="feather-search"></i>
    </div>
</div>

<div class="modal-body-table">
    <div class="table-responsive">
        <table class="table datanew table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Qty</th>
                    <th>{{ $priceColumnLabel }}</th>
                    @if($perLineTax)
                    <th class="line-tax-col">Taxable</th>
                    <th class="line-tax-col">Rate %</th>
                    <th class="line-tax-col">Line tax</th>
                    @endif
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody id="{{ $pickerPrefix }}_product_tbody" class="{{ $pickerPrefix }}-product-tbody"></tbody>
        </table>
    </div>
</div>
@if($perLineTax)
<div class="row mt-3">
    <div class="col-lg-6 offset-lg-6">
        <table class="table table-borderless mb-0">
            <tr><td>Sub Total</td><td class="text-end" id="{{ $pickerPrefix }}_sub_total">$0.00</td></tr>
            <tr><td id="{{ $pickerPrefix }}_tax_label">Tax</td><td class="text-end" id="{{ $pickerPrefix }}_tax">$0.00</td></tr>
            <tr><td><strong>Total</strong></td><td class="text-end"><strong id="{{ $pickerPrefix }}_total">$0.00</strong></td></tr>
        </table>
    </div>
</div>
@endif
