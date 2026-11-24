{{-- Reusable line rows for purchase documents. Set $grn true for GRN (qty_received / unit_cost). --}}
@php $rows = (int) ($lineCount ?? 5); @endphp
@for($i = 0; $i < $rows; $i++)
    @php
        $L = old('lines.' . $i, []);
        $qtyName = !empty($grn) ? 'qty_received' : 'qty';
        $priceName = !empty($grn) ? 'unit_cost' : 'unit_price';
    @endphp
    <tr>
        <td><input type="text" name="lines[{{ $i }}][product_id]" class="form-control form-control-sm" value="{{ $L['product_id'] ?? '' }}" placeholder="SKU (optional)"></td>
        <td><input type="text" name="lines[{{ $i }}][product_name]" class="form-control form-control-sm" value="{{ $L['product_name'] ?? '' }}" placeholder="Description"></td>
        <td><input type="number" min="1" name="lines[{{ $i }}][{{ $qtyName }}]" class="form-control form-control-sm" value="{{ $L[$qtyName] ?? $L['qty'] ?? '' }}" placeholder="Qty"></td>
        <td><input type="number" step="0.01" min="0" name="lines[{{ $i }}][{{ $priceName }}]" class="form-control form-control-sm" value="{{ $L[$priceName] ?? $L['unit_price'] ?? '' }}" placeholder="Price"></td>
    </tr>
@endfor
