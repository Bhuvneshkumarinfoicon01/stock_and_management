@foreach($stocks as $stock)
<tr>
    <td>
        <label class="checkboxs">
            <input type="checkbox" />
            <span class="checkmarks"></span>
        </label>
    </td>
    <td>{{ $stock->store->name }}</td>
    <td>
        <div class="productimgname">
            <a href="javascript:void(0);" class="product-img stock-img">
                <img
                    src="{{asset($stock->images ? 'storage/'.$stock->images : 'admin\assets\img\avatar\default-product.png')}}"
                    alt="product"
                />
            </a>
            <a href="javascript:void(0);">{{ $stock->product_name }}</a>
        </div>
    </td>
    <td>{{ $stock->sku }}</td>
    <td>{{ $stock->category->name }}</td>
    <td>${{ $stock->price }}</td>
    <td>{{ $stock->unit?->name }}</td>
    <td>{{ $stock->quantity }}</td>
    @php $user = auth()->user(); @endphp @if($user && $user->role_id === 1)
    <td class="action-table-data">
        <div class="edit-delete-action">
            <!-- <a class="me-2 edit-icon p-2" href="javascript:void(0);">
											<i data-feather="eye" class="feather-eye"></i>
										</a> -->
            <a
                class="me-2 p-2"
                href="{{ route('admin.stock.edit', $stock->id) }}"
            >
                <i data-feather="edit" class="feather-edit"></i>
            </a>
            {{--<a
                class="me-2 p-2"
                href="{{ route('stocks.edit', $stock->id) }}"
                data-bs-toggle="modal"
                data-bs-target="#edit-units"
            >
                <i data-feather="edit" class="feather-edit"></i> </a
            >--}}
            <a
                href="javascript:void(0);"
                class="delete-stock confirm-text p-2"
                data-id="{{ $stock->id }}"
            >
                <i data-feather="trash-2" class="feather-trash-2"></i>
            </a>
        </div>
    </td>
    @endif
</tr>
@endforeach
