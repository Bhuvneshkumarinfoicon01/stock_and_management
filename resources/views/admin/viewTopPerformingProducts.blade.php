@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title me-auto">
                <h4>Product Performance Report</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-lg-12 col-sm-12 col-12">
                <div class="card flex-fill default-cover w-100 mb-4">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h4 class="card-title mb-0">Top Performing Products</h4>
                       
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless best-seller">
                                <tbody>
                                    @foreach($viewAllTopPerformingProducts as $topProduct)
                                    <tr>
                                        <td>
                                            <div class="product-info">
                                                <a href="#" class="product-img">
                                                    <img
                                                    src="{{asset($topProduct->stock->images ? 'storage/'.$topProduct->stock->images : 'admin\assets\img\avatar\default-product.png')}}"
                                                        alt="product"
                                                    />
                                                </a>
                                                <div class="info">
                                                    <a href="#"
                                                        >{{$topProduct->product_name}}</a
                                                    >
                                                    <p class="dull-text">
                                                        ${{ number_format($topProduct->sold_amount, 2) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="head-text">Sales</p>
                                            {{$topProduct->sold_qty}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{$viewAllTopPerformingProducts->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

