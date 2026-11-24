@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title me-auto">
                <h4>Low On Stocks Report</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-lg-12 col-sm-12 col-12">
                <div class="card flex-fill default-cover w-100 mb-4">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h4 class="card-title mb-0">Low on stock products</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless best-seller">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Store</th>
                                        <th>Available Qty</th>
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStock as $product)
                                    <tr>
                                        <td>
                                            <div class="product-info">
                                                <a href="#" class="product-img">
                                                    <img src="{{asset($product->images ? 'storage/'.$product->images : 'admin\assets\img\avatar\default-product.png')}}" alt="product" />


                                                </a>
                                                <div class="info">
                                                    <a
                                                        href="#"
                                                        >{{ $product->product_name }}</a
                                                    >
                                                    <p class="dull-text">
                                                        ${{number_format($product->price,2)}}
                                                    </p>
                                                </div>

                                            </div>
                                        </td>
                                        <td>
                                            <p>
                                                {{ $product->store->name ?? 'N/A' }}
                                            </p>
                                        </td>
                                        <td>
                                            {{ $product->quantity }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{$lowStock->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
