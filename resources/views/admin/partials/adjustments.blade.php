
    @foreach($adjustments as $adjustment)
    <tr>
        <td>
            <label class="checkboxs">
                <input type="checkbox" />
                <span class="checkmarks"></span>
            </label>
        </td>

        <td>{{ $adjustment->store->name }}</td>
        <td>
            <div class="productimgname">
                <a href="javascript:void(0);" class="product-img stock-img">
                    <img src="{{ asset($adjustment->stock->images ? 'storage/'.$adjustment->stock->images : 'admin/assets/img/avatar/default-product.png') }}" alt="product" />
                </a>
                <a href="javascript:void(0);">{{ $adjustment->stock->product_name }}</a>
            </div>
        </td>
        <td>{{ $adjustment->adjust_quantity }}</td>
        <td>{{ $adjustment->reason }}</td>
        <td>{{ format_date($adjustment->adjust_date) }}</td>
        @php
        $user = auth()->user();
        @endphp
        @if($user && $user->role_id === 1)
        <td class="action-table-data">
            <div class="edit-delete-action">
                <a href="javascript:void(0);" class="edit-btn me-2 p-2" data-id="{{ $adjustment->id }}" data-bs-toggle="modal" data-bs-target="#edit-units">
                    <i data-feather="edit" class="feather-edit"></i>
                </a>
                <a class="confirm-text p-2 delete-adjustment" href="javascript:void(0);" data-id="{{ $adjustment->id }}">
                    <i data-feather="trash-2" class="feather-trash-2"></i>
                </a>
            </div>
        </td>
        @endif
    </tr>
    @endforeach
    
