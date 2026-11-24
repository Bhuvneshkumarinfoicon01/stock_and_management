@foreach($outofstockProducts as $outofstockProduct)
<tr>
    <td><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
    <td>{{$outofstockProduct->store->name}}</td>
    <td>
        <div class="productimgname">
            <a href="javascript:void(0);" class="product-img stock-img">
                <img src="{{asset($outofstockProduct->images ? 'storage/'.$outofstockProduct->images : 'admin\assets\img\avatar\default-product.png')}}" alt="product">
            </a>
            <a href="javascript:void(0);">{{$outofstockProduct->product_name}}</a>
        </div>
    </td>
    <td>{{$outofstockProduct->category->name}}</td>
    <td>{{$outofstockProduct->sku}}</td>
    <td>{{$outofstockProduct->quantity}}</td>
    <td>10</td>
</tr>
@endforeach
