@foreach($lowStockProducts as $lowStockProduct)
<tr>
    <td><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
    <td>{{$lowStockProduct->store->name}}</td>
    <td>
        <div class="productimgname">
            <a href="javascript:void(0);" class="product-img stock-img">
                <img src="{{asset($lowStockProduct->images ? 'storage/'.$lowStockProduct->images : 'admin\assets\img\avatar\default-product.png')}}" alt="product">
            </a>
            <a href="javascript:void(0);">{{$lowStockProduct->product_name}}</a>
        </div>
    </td>
    <td>{{$lowStockProduct->category->name}}</td>
    <td>{{$lowStockProduct->sku}}</td>
    <td>{{$lowStockProduct->quantity}}</td>
    <td>10</td>
</tr>
@endforeach

