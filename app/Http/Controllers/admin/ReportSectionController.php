<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportSectionController extends Controller
{
    public function index()
    {
        $storeId = session('selected_store_id');
        $orders = Order::with(['orderItems', 'customer'])
            ->where('store_id', $storeId)
            ->get();
        return view('storemanager.report-section', compact('orders'));
    }

    public function customerInsightsView(Request $request)
    {
        // $storeId = session('selected_store_id');
        $countryList = config('countries');
        if ($request->ajax()) {
            $query = Customer::query();

            if ($request->has('country') && $request->country !== 'choose') {
                $query->where('country', $request->country);
            }
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->order_type);
            }

            $customer = $query->get();
            return datatables()
                ->eloquent($query)
                ->editColumn('customer_name', fn($row) => $row->customer_name)
                ->editColumn('code', fn($row) => $row->id)
                ->editColumn('email', fn($row) => $row->email)
                ->editColumn('phone', fn($row) => $row->phone)
                ->editColumn('city', fn($row) => $row->city)
                ->editColumn('country', fn($row) => $row->country)
                ->editColumn('action', function ($customer) {
                    return '
                            <div class="edit-delete-action">
                                <a class="me-2 p-2 viewCustomer" href="#" data-id="' . $customer->id . '" 
                                  data-bs-toggle="modal" data-bs-target="#view-customer">         
                                  <i data-feather="eye" class="feather-eye"></i>
                                </a>                 
                                <a class="me-2 p-2 editCustomer" href="#" data-id="' . $customer->id . '"
                                   data-bs-toggle="modal" data-bs-target="#edit-units">         
                                    <i data-feather="edit" class="feather-edit"></i>
                                 </a>                   
                                <a class="confirm-text p-2 deleteCustomer" href="javascript:void(0);"
                                    data-id="' . $customer->id . '">        
                                    <i data-feather="trash-2" class="feather-trash-2" ></i >    
                                 </a>                   
                            </div>                    
                        ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('storemanager.customer-insights', compact('countryList'));
    }
}
