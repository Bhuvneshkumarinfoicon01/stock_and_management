@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Employees</h4>
                    <h6>Manage your employees</h6>
                </div>
            </div>
            <ul class="table-top-head">
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i
                            data-feather="chevron-up" class="feather-chevron-up"></i></a>
                </li>
            </ul>
            @if (hasPermission('create', 'user'))
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#add-employee">
                    <i data-feather="plus-circle" class="me-2"></i>Add New Employee
                </a>
            </div>
            @endif
        </div>

        <div class="card table-list-card">
            <div class="card-body pb-0">
                <div class="table-top table-top-two table-top-new">
                    <div class="search-set mb-0">
                        <div class="total-employees">
                            <h6>
                                <i data-feather="users" class="feather-user"></i>
                                Total Employees <span id="employee-count"></span>
                            </h6>
                        </div>
                    </div>
                    <div class="search-path d-flex align-items-center search-path-new">

                        <div class="form-sort">
                            <i data-feather="sliders" class="info-img"></i>
                            <select class="select" id="sort-order">
                                <option value="">Sort by</option>
                                <option value="desc">Newest First</option>
                                <option value="asc">Oldest First</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- /Filter -->
                <div class="card" id="filter_inputs">
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="search-set">
                                    <div class="search-input d-flex align-items-center">
                                        <div id="custom-search" class="me-2"></div>
                                        <a href="#" class="btn btn-searchset">
                                            <i data-feather="search" class="feather-search"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i data-feather="stop-circle" class="info-img"></i>
                                    <select id="status-filter" class="select user-status">
                                        <option value="choose">Choose Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- /Filter -->
                <div class="table-responsive">
                    <table class="table datanew users-table" id="employee-list-table">
                        <thead>
                            <tr>
                                <!-- <th class="no-sort">
                                    <label class="checkboxs">
                                        <input
                                            type="checkbox"
                                            id="select-all"
                                        />
                                        <span class="checkmarks"></span>
                                    </label>
                                </th> -->
                                <th>Employee</th>
                                <th>Employee ID</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Status</th>
                                <th class="no-sort">Action</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- employee-edit -->
<div class="modal fade" id="employee-edit" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered stock-adjust-modal">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">
                        <div class="page-title">
                            <h4>Edit Employee</h4>
                        </div>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body custom-modal-body">
                        <form id="employee-edit-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" />
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="profile-pic-upload edit-pic new-employee-field">
                                        <div class="profile-pic">
                                            <span><img id="employee-image-preview" name="file" src="" alt="" /></span>
                                        </div>
                                        <div class="me-3 mb-0">
                                            <div class="image-upload mb-0">
                                                <input type="file" name="file" id="employee-image-input"
                                                    accept="image/*" onchange="previewEmployeeImage(this)" />
                                                <div class="image-uploads">
                                                    <h4>Change Image</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select id="role-edit" name="role_id" id="role_id" class="form-select">
                                            <option value="">Select Role</option>
                                            @php
                                            $authRoleId = Auth::user()->role_id;
                                            @endphp

                                            @foreach($roles as $role)
                                            @if($authRoleId == 1)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @elseif($authRoleId == 2 && $role->id != 1 && $role->id != 2)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @elseif($authRoleId == 3 )
                                            <option value="5">Clerk</option>
                                            @endif
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-role_id"></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="first_name" />
                                        <span class="text-danger error-first_name"></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="last_name" />
                                        <span class="text-danger error-last_name"></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" name="username" />
                                        <span class="text-danger error-username"></span>
                                    </div>

                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" />
                                        <span class="text-danger error-email"></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Contact Number</label>
                                        <input type="text" class="form-control" name="phone" />
                                        <span class="text-danger error-phone"></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Emp Code</label>
                                        <input type="text" class="form-control" name="emp_code" />
                                        <span class="text-danger error-emp_code"></span>
                                    </div>
                                </div>


                                <div class="col-lg-4 col-md-6">
                                    <div class="input-blocks">
                                        <label class="form-label">Joining Date</label>
                                        <div class="input-groupicon calender-input">
                                            <i data-feather="calendar" class="info-img"></i>
                                            <input type="text" class="datetimepicker form-control"
                                                placeholder="Select Date" name="joining_date" />
                                        </div>
                                        <span class="text-danger error-joining_date"></span>
                                    </div>
                                </div>

                                <div id="store-field-edit" class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Assign Store</label>
                                        <select name="store" id="store" class="form-select">
                                            <option value="">
                                                Select Store
                                            </option>
                                            @foreach($stores as $store)
                                            <option value="{{ $store->id }}">
                                                {{ $store->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-store"></span>
                                    </div>
                                </div>
                                <div id="discount-field-edit" class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Assign Max Discount</label>
                                        <select name="discount_percentage" id="discount_percentage" class="form-select">
                                            @for($i = 0; $i <= 100; $i +=5) <option value="{{ $i }}" {{
                                                $i==old('max_discount', $store->max_discount ?? 0) ? 'selected' : '' }}>
                                                {{ $i }}%
                                                </option>
                                                @endfor

                                        </select>
                                        <span class="text-danger error-discount_percentage"></span>
                                    </div>
                                </div>


                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="other-info">
                                        <div class="card-title-head">
                                            <h6>
                                                <span><i data-feather="info" class="feather-edit"></i></span>Other
                                                Information
                                            </h6>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="input-blocks">
                                                    <label class="form-label">Date of Birth</label>

                                                    <div class="input-groupicon calender-input">
                                                        <i data-feather="calendar" class="info-img"></i>
                                                        <input type="text" class="datetimepicker form-control"
                                                            placeholder="Select Date" name="dob" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Gender</label>
                                                    <select class="select" name="gender">
                                                        <option value="">
                                                            Select Gender
                                                        </option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">
                                                            Female
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Nationality</label>
                                                    <select class="select" name="nationality">
                                                        <option value="" selected>Select Nationality</option>
                                                        @foreach($countryList as $country)
                                                        <option value="{{$country}}">
                                                            {{$country}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Emergency No 1</label>
                                                    <input type="text" class="form-control" name="emergency_no1" />
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Emergency No 2</label>
                                                    <input type="text" class="form-control" name="emergency_no2" />
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6"></div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Address</label>
                                                    <input type="text" class="form-control" name="address" />
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Country</label>
                                                    <select class="select" name="country">
                                                        <option value="" selected>Select Country</option>
                                                        @foreach($countryList as $country)
                                                        <option value="{{$country}}">
                                                            {{$country}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">State</label>
                                                    <input type="text" class="form-control" name="state" />
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">City</label>
                                                    <input type="text" class="form-control" name="city" />
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Zipcode</label>
                                                    <input type="text" class="form-control" name="zipcode" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer-btn">
                                <button type="button" class="btn btn-cancel me-2" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-submit">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- employee-view -->
<div class="modal fade" id="employee-view" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered stock-adjust-modal">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">
                        <div class="page-title">
                            <h4>Employee View</h4>
                        </div>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body custom-modal-body">
                        <form>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="profile-pic-upload edit-pic new-employee-field">
                                        <div class="profile-pic">
                                            <span><img name="file" src="" alt="" /></span>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select name="role_id" id="role-view" class="form-select">
                                            @foreach($roles as $role)
                                            <option value="{{ $role->id }}">
                                                {{ $role->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">First Name</label>
                                        <input type="text" name="first_name" disabled class="form-control" value="" />
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" name="last_name" disabled class="form-control" value="" />
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" name="username" disabled class="form-control" value="" />
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="" />
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Contact Number</label>
                                        <input type="text" name="phone" disabled class="form-control" value="" />
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Emp Code</label>
                                        <input type="text" disabled name="emp_code" class="form-control" value="" />
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="input-blocks">
                                        <label class="form-label">Date of Birth</label>

                                        <div class="input-groupicon calender-input">
                                            <i data-feather="calendar" class="info-img"></i>
                                            <input type="text" disabled name="dob" class="datetimepicker form-control"
                                                placeholder="Select Date" value="" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Gender</label>
                                        <select class="select" name="gender">
                                            <option value="Male">Male</option>
                                            <option value="Female">
                                                Female
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nationality</label>
                                        <select class="select" name="nationality">
                                            <option value="" selected>Select Nationality</option>
                                            @foreach($countryList as $country)
                                            <option value="{{$country}}">
                                                {{$country}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="input-blocks">
                                        <label class="form-label">Joining Date</label>
                                        <div class="input-groupicon calender-input">
                                            <i data-feather="calendar" class="info-img"></i>
                                            <input type="text" name="joining_date" disabled
                                                class="datetimepicker form-control" placeholder="Select Date"
                                                value="" />
                                        </div>
                                    </div>
                                </div>

                                <div id="store-field-view" class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Assign Store</label>
                                        <select name="store" id="store" class="form-select">
                                            @foreach($stores as $store)
                                            <option value="{{ $store->id }}">
                                                {{ $store->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="other-info">
                                        <div class="card-title-head">
                                            <h6>
                                                <span><i data-feather="info" class="feather-edit"></i></span>Other
                                                Information
                                            </h6>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Emergency No 1</label>
                                                    <input type="text" disabled class="form-control"
                                                        name="emergency_no1" value="" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Emergency No 2</label>
                                                    <input type="text" disabled class="form-control"
                                                        name="emergency_no2" value="" />
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Address</label>
                                                    <input type="text" disabled class="form-control" name="address"
                                                        value="" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Country</label>
                                                    <select class="select" name="country">
                                                        <option value="" selected>Select Country</option>
                                                        @foreach($countryList as $country)
                                                        <option value="{{$country}}">
                                                            {{$country}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">State</label>
                                                    <input type="text" disabled class="form-control" name="state"
                                                        value="" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">City</label>
                                                    <input type="text" disabled class="form-control" name="city"
                                                        value="" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Zipcode</label>
                                                    <input type="text" disabled class="form-control" name="zipcode"
                                                        value="" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer-btn">
                                <button type="button" class="btn btn-cancel me-2" data-bs-dismiss="modal">
                                    Close
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- add-employee -->
<div class="modal fade" id="add-employee" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered stock-adjust-modal">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">
                        <div class="page-title">
                            <h4>Add New Employee</h4>
                        </div>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body custom-modal-body new-employee-field">
                        <form id="add-employee-form" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="profile-pic-upload edit-pic">
                                        <div class="profile-pic">
                                            <span><img id="preview-image" src="" alt="" /></span>
                                        </div>
                                        <div class="me-3 mb-0">
                                            <div class="image-upload mb-0">
                                                <input type="file" name="file" id="file-input" accept="image/*" />
                                                <div class="image-uploads">
                                                    <h4>Upload Image</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select id="role" name="role_id" class="form-select">
                                            <option value="">Select Role</option>
                                            @if($user->role_id==1)
                                            <option value="1">Super Admin</option>
                                            <option value="2">Admin</option>
                                            <option value="3">Store Manager</option>
                                            <option value="5">Clerk</option>
                                            @elseif($user->role_id==2)
                                            <option value="3">Store Manager</option>
                                            <option value="5">Clerk</option>
                                            @else
                                            <!-- <option value="2">Admin</option> -->
                                            <!-- <option value="3">Store Manager</option> -->
                                            <option value="5">Clerk</option>
                                            @endif
                                        </select>
                                        <span class="text-danger error-role_id"></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" placeholder="First Name"
                                            name="first_name" />
                                        <span class="text-danger error-first_name"></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" placeholder="Last Name"
                                            name="last_name" />
                                        <span class="text-danger error-last_name"></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" placeholder="Username"
                                            name="username" />
                                        <span class="text-danger error-username"></span>
                                    </div>

                                </div>
                                <div id="password-field" class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="text" class="form-control" placeholder="Password"
                                            name="password" />

                                        <span class="text-danger error-password"></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" placeholder="Email" name="email" />
                                        <span class="text-danger error-email"></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Contact Number</label>
                                        <input type="text" class="form-control" placeholder="Contact No."
                                            name="phone" />
                                        <span class="text-danger error-phone"></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Emp Code</label>
                                        <input type="text" class="form-control" placeholder="Employee Code"
                                            name="emp_code" />
                                        <span class="text-danger error-emp_code"></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="input-blocks">
                                        <label class="form-label">Joining Date</label>
                                        <div class="input-groupicon calender-input">
                                            <i data-feather="calendar" class="info-img"></i>
                                            <input type="text" class="datetimepicker form-control"
                                                placeholder="Select Date" name="joining_date" />

                                        </div>
                                        <span class="text-danger error-joining_date"></span>
                                    </div>
                                </div>
                                <div id="store-field" class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Assign Store</label>
                                        <select name="store" id="store" class="form-select">
                                            <option value="">Select Store</option>
                                            @foreach($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-store"></span>
                                    </div>
                                </div>
                                <div id="discount-field" class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Assign Max Discount</label>
                                        <select name="discount_percentage" id="discount_percentage" class="form-select">
                                            @for($i = 0; $i <= 100; $i +=5) <option value="{{ $i }}">{{ $i }}%</option>
                                                @endfor
                                        </select>
                                        <span class="text-danger error-discount_percentage"></span>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="other-info">
                                        <div class="card-title-head">
                                            <h6>
                                                <span><i data-feather="info" class="feather-edit"></i></span>Other
                                                Information
                                            </h6>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="input-blocks">
                                                    <label class="form-label">Date of Birth</label>

                                                    <div class="input-groupicon calender-input">
                                                        <i data-feather="calendar" class="info-img"></i>
                                                        <input type="text" class="datetimepicker form-control"
                                                            placeholder="Select Date" name="dob" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Gender</label>
                                                    <select class="select" name="gender">
                                                        <option value="">
                                                            Select Gender
                                                        </option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">
                                                            Female
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Nationality</label>
                                                    <select class="select" name="nationality">
                                                        <option value="">Choose Nationality</option>
                                                        @foreach($countryList as $country)
                                                        <option value="{{$country}}">
                                                            {{$country}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Emergency No 1</label>
                                                    <input type="text" class="form-control" name="emergency_no1"
                                                        placeholder="Emergency No 1" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Emergency No 2</label>
                                                    <input type="text" class="form-control" name="emergency_no2"
                                                        placeholder="Emergency No 2" />
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Address</label>
                                                    <input type="text" class="form-control" placeholder="Address"
                                                        name="address" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Country</label>
                                                    <select class="select" name="country">
                                                        <option value="" selected>Select Country</option>
                                                        @foreach($countryList as $country)
                                                        <option value="{{$country}}">
                                                            {{$country}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">State</label>
                                                    <input type="text" class="form-control" placeholder="State"
                                                        name="state" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">City</label>
                                                    <input type="text" class="form-control" placeholder="City"
                                                        name="city" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Zipcode</label>
                                                    <input type="text" class="form-control" placeholder="Zipcode"
                                                        name="zipcode" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer-btn">
                                <button type="button" class="btn btn-cancel me-2" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-submit">
                                    Create
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- permission-to-employee -->
<!-- Permission Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header custom-modal-header">
                <h5 class="modal-title">Manage Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="permissionForm">
                <input type="hidden" name="user_id" id="permission_user_id">
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered permissions-table mb-0">
                            <thead>
                                <tr>
                                    <th>Module</th>
                                    <th>Create</th>
                                    <th>Update</th>
                                    <th>Delete</th>
                                    <th>Allow All</th>
                                </tr>
                            </thead>
                            <tbody id="permissionTableBody">
                                @foreach($permissions as $module => $actions)
                                @if(!(isset($user) && $user->role_id == 5 && in_array($module, ['user', 'permission',
                                'store'])))
                                <tr>
                                    <td class="text-capitalize">{{ $module }}</td>

                                    @foreach(['create', 'update', 'delete'] as $action)
                                    @php
                                    $permExists = $actions->firstWhere('action', $action);
                                    @endphp
                                    @if($permExists)
                                    <td class="text-center">
                                        <input type="checkbox" name="permissions[]" value="{{ $permExists->id }}"
                                            class="perm-checkbox {{ $module }}-perm">
                                    </td>
                                    @else
                                    <td></td> {{-- Empty cell if no permission exists --}}
                                    @endif
                                    @endforeach

                                    <td class="text-center">
                                        <input type="checkbox" class="allow-all" data-module="{{ $module }}">
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>


                        </table>
                    </div>
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Permissions</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sidebar links for clerks -->
<div class="modal fade" id="sidebarLinksModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header custom-modal-header">
                <h5 class="modal-title">Manage Sidebar Links</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sidebarLinksForm">
                <input type="hidden" name="clerk_id" id="sidebar_clerk_id" />
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered permissions-table mb-0">
                            <thead>
                                <tr>
                                    <th>Link</th>
                                    <th>Description</th>
                                    <th>Select</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- <tr>
                                    <td class="text-capitalize">Dashboard -> Sales Summary</td>
                                    <td>Overview of sales</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_dashboard_sales_summary"
                                            value="dashboard_sales_summary" />
                                    </td>
                                </tr> -->
                                <tr>
                                    <td class="text-capitalize">Dashboard -> Invantory Summary</td>
                                    <td>Overview of inventory and stocks</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_dashboard_inventory_summary"
                                            value="dashboard_inventory_summary" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">Dashboard -> Report Section</td>
                                    <td>Reports of sales and orders</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_dashboard_report_section"
                                            value="dashboard_report_section" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">Dashboard -> Customer Insights</td>
                                    <td>Customers information</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_dashboard_customer_insight"
                                            value="dashboard_customer_insight" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">Sales Transactions</td>
                                    <td>Recent transaction information</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_sales_transaction"
                                            value="sales_transaction" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">Sales Report</td>
                                    <td>Recent Orders and transactions</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_sales_report"
                                            value="sales_report" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">Customer Interaction</td>
                                    <td>Add Edit Delete Customer Information</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_customer_interaction"
                                            value="customer_interaction" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">All Stocks</td>
                                    <td>All Stocks list with add/edit/delete actions</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_all_stock" value="all_stock" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">Add Stocks</td>
                                    <td>Add new stocks into a store</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_add_stock" value="add_stock" />
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-capitalize">Stock Transfer</td>
                                    <td>Manage stock movement between stores.</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_stock_transfer"
                                            value="stock_transfer" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">Stock Adjustment</td>
                                    <td>Adjust stock quantities and corrections.</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_stock_adjustment"
                                            value="stock_adjustment" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">Incoming Stocks</td>
                                    <td>Accepting or Rejecting incoming stocks from other stores</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_incoming_stock"
                                            value="incoming_stock" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">Outgoing Stocks</td>
                                    <td>Overview of outgoing stocks from our stores</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_outgoing_stock"
                                            value="outgoing_stock" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">Real Time Updates</td>
                                    <td>Overview of out of stock and low on stocks products</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_real_time_updates"
                                            value="real_time_updates" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">Configure POS Terminal</td>
                                    <td>Creating Orders</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_configure_pos_terminal"
                                            value="configure_pos_terminal" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">Invoice Management</td>
                                    <td>Creating Manual Orders and Invoice</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_invoice_management"
                                            value="invoice_management" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">Transaction Management</td>
                                    <td>Gives us all the transactions done and their Receipt</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_transaction_management"
                                            value="transaction_management" />
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-capitalize">Sales Insights</td>
                                    <td>Graphs and Charts for all the sales</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_sales_insight"
                                            value="sales_insight" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">All Stores</td>
                                    <td>List of all stores , Add/Edit/Delete Stores</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_all_store" value="all_store" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">Communications</td>
                                    <td>Chat with higher authority users</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_communication"
                                            value="communication" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">Daily Operation Overview</td>
                                    <td>Overview of daily operations like orders , sales , transfers</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_daily_operation_overview"
                                            value="daily_operation_overview" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">Profit and Loss</td>
                                    <td>Overview of profit and loss made by each store</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_profit_and_loss"
                                            value="profit_and_loss" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize">Balance Sheet</td>
                                    <td>Access the balance sheet</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" id="link_balance_sheet"
                                            value="balance_sheet" />
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td class="text-capitalize">Reports</td>
                                    <td>View and generate reports.</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="links[]" value="reports" />
                                    </td> -->
                                </tr>
                                <!-- Add more links as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Sidebar Links</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if (auth()->user()->role_id == 1)
<div class="modal fade" id="resetPasswordModal" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 custom-modal-header">
                <div class="page-title">
                    <h4>Reset password</h4>
                    <h6 class="text-muted mb-0">For: <span id="reset_password_user_name"></span></h6>
                </div>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="reset-password-form" class="p-3 pt-0">
                <input type="hidden" id="reset_password_user_id" name="user_id" value="" />
                <div class="input-blocks mb-3">
                    <label class="form-label">New password <span class="text-danger">*</span></label>
                    <input type="password" name="password" id="reset_password_new" class="form-control" required autocomplete="new-password" minlength="8" />
                </div>
                <div class="input-blocks mb-3">
                    <label class="form-label">Confirm password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" id="reset_password_confirm" class="form-control" required autocomplete="new-password" minlength="8" />
                </div>
                <div class="modal-footer border-0 px-0 pb-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif






@endsection @section('scripts')
<script>
    $(document).ready(function () {
        let table = $('#employee-list-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.users') }}",
                data: function (d) {
                    d.status = $('#status-filter').val();
                    d.order_type = $('#sort-order').val();
                }
            },
            columns: [
                { data: 'user_profile', name: 'name' },
                { data: 'user_id', name: 'id' },
                { data: 'email', name: 'email', orderable: false, searchable: false },
                { data: 'phone', name: 'phone', orderable: false, searchable: false },
                { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
            ],
            pageLength: 10,
            drawCallback: function () {
                feather.replace();
                // Update employee count
                let info = this.api().page.info();
                $('#employee-count').text(info.recordsDisplay);
            },
            initComplete: function () {
                let searchWrapper = $('#employee-list-table_filter');
                let defaultSearchBox = searchWrapper.find('input');
                $('#custom-search').append(defaultSearchBox);
                defaultSearchBox.attr('placeholder', 'Search...');
                searchWrapper.remove();
            }
        });

        $('#status-filter').on('change', function () {
            table.ajax.reload();
        });
        $('#sort-order').on('change', function () {
            table.ajax.reload();
        });
    });

    @if (auth()->user()->role_id == 1)
    @php
        $resetPasswordUrlTpl = route('admin.reset-user-password', ['id' => '__USER_ID__']);
    @endphp
    $(function () {
        var resetPasswordUrlTpl = @json($resetPasswordUrlTpl);
        $(document).on('click', '.resetUserPassword', function () {
            $('#reset_password_user_id').val($(this).data('id'));
            $('#reset_password_user_name').text($(this).data('name'));
            $('#reset_password_new, #reset_password_confirm').val('');
        });
        $(document).on('submit', '#reset-password-form', function (e) {
            e.preventDefault();
            var userId = $('#reset_password_user_id').val();
            $.ajax({
                url: resetPasswordUrlTpl.replace('__USER_ID__', userId),
                method: 'POST',
                data: {
                    password: $('#reset_password_new').val(),
                    password_confirmation: $('#reset_password_confirm').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (res.success) {
                        Swal.fire('Success', res.message, 'success');
                        var el = document.getElementById('resetPasswordModal');
                        if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            var inst = bootstrap.Modal.getInstance(el);
                            if (inst) inst.hide();
                        }
                        $('#employee-list-table').DataTable().ajax.reload(null, false);
                    }
                },
                error: function (xhr) {
                    var msg = 'Something went wrong.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        });
    });
    @endif

</script>
<!-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const passwordField = document.getElementById('password-field');

        function togglePasswordField() {
            const selectedRole = parseInt(roleSelect.value);
            if ([1, 2, 3].includes(selectedRole)) {
                passwordField.style.display = 'block';
            } else {
                passwordField.style.display = 'none';
            }
        }

        roleSelect.addEventListener('change', togglePasswordField);

        // Initial check in case of pre-selected value
        togglePasswordField();
    });
</script> -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        // const passwordField = document.getElementById('password-field');
        const storeField = document.getElementById('store-field');
        const discountField = document.getElementById('discount-field');

        function toggleFields() {
            const selectedRole = parseInt(roleSelect.value);
            if ([3, 5].includes(selectedRole)) {
                storeField.style.display = 'block';
            } else {
                storeField.style.display = 'none';
            }

            if ([5].includes(selectedRole)) {
                discountField.style.display = 'block';
            } else {
                discountField.style.display = 'none';
            }
        }

        roleSelect.addEventListener('change', toggleFields);

        // Initial load check
        toggleFields();
    });
</script>

<script>
    $(document).ready(function () {

        $(document).on("click", ".viewEmployee", function () {
            var empId = $(this).data("id");

            $.ajax({
                url: "{{ route('view-employee') }}",
                type: "GET",
                data: {
                    empId: empId,
                },
                success: function (response) {
                    console.log(response);

                    if (response.success) {
                        $('#employee-view img[name="file"]').attr(
                            "src",
                            "storage/" + response.data.profile_image
                        );
                        $('#employee-view input[name="first_name"]').val(
                            response.data.first_name
                        );
                        $('#employee-view input[name="last_name"]').val(
                            response.data.last_name
                        );
                        $('#employee-view input[name="username"]').val(
                            response.data.username
                        );
                        $('#employee-view input[name="email"]').val(
                            response.data.email
                        );
                        $('#employee-view input[name="phone"]').val(
                            response.data.phone
                        );
                        $('#employee-view input[name="emp_code"]').val(
                            response.data.emp_code
                        );
                        $('#employee-view input[name="dob"]')
                            .val(response.data.dob)
                            .trigger("change");
                        $('#employee-view select[name="gender"]')
                            .val(response.data.gender)
                            .trigger("change");
                        $('#employee-view select[name="nationality"]')
                            .val(response.data.nationality)
                            .trigger("change");
                        $('#employee-view input[name="joining_date"]')
                            .val(response.data.joining_date)
                            .trigger("change");
                        $('#employee-view select[name="role_id"]')
                            .val(response.data.role_id)
                            .trigger("change");
                        $('#employee-view select[name="store"]')
                            .val(response.data.assigned_store)
                            .trigger("change");
                        $('#employee-view input[name="emergency_no1"]').val(
                            response.data.emergency_no1
                        );
                        $('#employee-view input[name="emergency_no2"]').val(
                            response.data.emergency_no2
                        );
                        $('#employee-view input[name="address"]').val(
                            response.data.address
                        );
                        $('#employee-view select[name="country"]')
                            .val(response.data.country)
                            .trigger("change");
                        $('#employee-view input[name="state"]').val(
                            response.data.state
                        );
                        $('#employee-view input[name="city"]').val(
                            response.data.city
                        );
                        $('#employee-view input[name="zipcode"]').val(
                            response.data.zipcode
                        );

                        $("#employee-view").modal("show");
                    } else {
                        alert("Employee details not found!");
                    }
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                },
            });
        });

        // Edit Employee with AJAX
        $(document).on("click", ".editEmployee", function (e) {
            e.preventDefault();

            var empId = $(this).data("id");
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
            $.ajax({
                url: "/edit-employee/" + empId,
                type: "GET",

                success: function (response) {
                    if (response.success) {
                        console.log(response);
                        $("#employee-edit").on("shown.bs.modal", function () {
                            $('#employee-edit img[name="file"]').attr(
                                "src",
                                "storage/" + response.data.profile_image
                            );
                            $("#employee-edit")
                                .find('input[name="id"]')
                                .val(response.data.id || "").trigger('change');
                            $("#employee-edit")
                                .find('input[name="first_name"]')
                                .val(response.data.first_name || "");
                            $("#employee-edit")
                                .find('input[name="last_name"]')
                                .val(response.data.last_name || "");
                            $("#employee-edit")
                                .find('input[name="email"]')
                                .val(response.data.email || "");
                            $("#employee-edit")
                                .find('input[name="username"]')
                                .val(response.data.username || "");
                            $("#employee-edit")
                                .find('input[name="phone"]')
                                .val(response.data.phone || "");
                            $("#employee-edit")
                                .find('input[name="emp_code"]')
                                .val(response.data.emp_code || "");
                            $("#employee-edit")
                                .find('input[name="dob"]')
                                .val(response.data.dob || "")
                                .trigger("change");
                            $("#employee-edit")
                                .find('input[name="joining_date"]')
                                .val(response.data.joining_date || "")
                                .trigger("change");
                            $("#employee-edit")
                                .find('input[name="emergency_no1"]')
                                .val(response.data.emergency_no1 || "");
                            $("#employee-edit")
                                .find('input[name="emergency_no2"]')
                                .val(response.data.emergency_no2 || "");
                            $("#employee-edit")
                                .find('input[name="address"]')
                                .val(response.data.address || "");
                            $("#employee-edit")
                                .find('input[name="city"]')
                                .val(response.data.city || "");
                            $("#employee-edit")
                                .find('input[name="state"]')
                                .val(response.data.state || "");
                            $("#employee-edit")
                                .find('input[name="zipcode"]')
                                .val(response.data.zipcode || "");
                            $("#employee-edit")
                                .find('select[name="gender"]')
                                .val(response.data.gender || "")
                                .trigger("change");
                            $("#employee-edit")
                                .find('select[name="nationality"]')
                                .val(response.data.nationality || "")
                                .trigger("change");
                            $("#employee-edit")
                                .find('select[name="country"]')
                                .val(response.data.country || "")
                                .trigger("change");
                            $("#employee-edit")
                                .find('select[name="role_id"]')
                                .val(response.data.role_id || "");
                            $("#employee-edit")
                                .find('select[name="store"]')
                                .val(response.data.assigned_store || "").trigger('change');
                            $("#employee-edit")
                                .find('select[name="discount_percentage"]')
                                .val(response.data.max_discount || "").trigger('change');

                            $("#employee-edit").modal("show");
                        });

                        $("#employee-edit").modal("show");
                    } else {
                        alert("Error loading employee data!");
                    }
                },

                error: function (xhr) {
                    console.log(xhr.responseText);
                },
            });
        });

        // Update employee(users-table) with AJAX after submitting the editted data
        $("#employee-edit-form").on("submit", function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            var fileInput = $('input[name="file"]')[0];
            if (fileInput.files.length === 0) {
                var existingProfileImage = $("#employee-edit")
                    .find("img[name='file']")
                    .attr("src");
                formData.append("existing_profile_image", existingProfileImage);
            }

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            $.ajax({
                url: "{{ route('update-employee') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        swal.fire({
                            title: "Success!",
                            text: "Employee updated successfully!",
                            icon: "success",

                        }).then(() => {
                            $("#employee-edit").modal("hide");
                            location.reload();
                        });


                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Error! Employee Not Found",
                            confirmButtonColor: "#d33",
                        });
                    }
                },

                error: function (xhr, status, error) {
                    console.error("Error:", xhr.responseText);
                    // Clear previous errors
                    $("#employee-edit-form .text-danger").text("");

                    let response = JSON.parse(xhr.responseText);
                    if (response.errors) {
                        $.each(response.errors, function (key, messages) {
                            // Show the first error message for each field
                            $(".error-" + key).text(messages[0]);
                        });
                    } else if (response.message) {
                        Swal.fire({
                            icon: "error",
                            title: "Validation Error",
                            text: response.message,
                            confirmButtonColor: "#d33",
                        });
                    }
                },
            });
        });
    });
    // Add Employee Form Submission
    $(document).ready(function () {
        $("#add-employee-form").on("submit", function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            console.log("Form Data:");
            for (let [key, value] of formData.entries()) {
                console.log(`${key}:`, value);
            }
            if (!formData.get("file")) {
                console.error("No file selected!");
            }
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
            $.ajax({
                url: "{{route('add-employee')  }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        swal.fire({
                            title: "Success!",
                            text: "Employee added successfully!",
                            icon: "success",

                        }).then(() => {
                            $("#add-employee").modal("hide");
                            location.reload();
                        });

                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error:", xhr.responseText);
                    // Clear previous errors
                    $("#add-employee-form .text-danger").text("");

                    let response = JSON.parse(xhr.responseText);
                    if (response.errors) {
                        $.each(response.errors, function (key, messages) {
                            // Show the first error message for each field
                            $(".error-" + key).text(messages[0]);
                        });
                    } else if (response.message) {
                        Swal.fire({
                            icon: "error",
                            title: "Validation Error",
                            text: response.message,
                            confirmButtonColor: "#d33",
                        });
                    }
                },
            });
        });
    });

    $(document).ready(function () {
        $("#file-input").change(function (event) {
            var reader = new FileReader();
            reader.onload = function () {
                $("#preview-image").attr("src", reader.result);
            };
            reader.readAsDataURL(event.target.files[0]);
        });
    });

    function previewEmployeeImage(input) {
        var preview = document.getElementById("employee-image-preview");

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }


    // $(document).ready(function () {
    //     function toggleEditStoreField() {
    //         const selectedRoleEdit = parseInt($("#role-edit").val());
    //         const storeFieldEdit = $("#store-field-edit");
    //         if ([3, 5].includes(selectedRoleEdit)) {
    //             storeFieldEdit.show();
    //         } else {
    //             storeFieldEdit.hide();
    //         }
    //     }

    //     // On role change in edit modal
    //     $(document).on('change', "#role-edit", toggleEditStoreField);

    //     // When opening the modal, after setting the role value, call the toggle
    //     $('#employee-edit').on('shown.bs.modal', function () {
    //         toggleEditStoreField();
    //     });

    // });
    // // Toggle the Discount dropdown for clerk
    // $(document).ready(function () {
    //     function toggleEditStoreField() {
    //         const selectedRoleEdit = parseInt($("#role-edit").val());
    //         const storeFieldEdit = $("#discount-field-edit");
    //         if ([5].includes(selectedRoleEdit)) {
    //             storeFieldEdit.show();
    //         } else {
    //             storeFieldEdit.hide();
    //         }
    //     }

    //     // On role change in edit modal
    //     $(document).on('change', "#role-edit", toggleEditStoreField);

    //     // When opening the modal, after setting the role value, call the toggle
    //     $('#employee-edit').on('shown.bs.modal', function () {
    //         toggleEditStoreField();
    //     });

    // });
    $(function () {
        const $role = $('#role-edit');
        const $storeField = $('#store-field-edit');       // shown for roles 3 & 5
        const $discountField = $('#discount-field-edit'); // shown for role 5

        function updateEditFields() {
            if (!$role.length) return;
            const val = parseInt($role.val(), 10);
            // hide if invalid
            if (Number.isNaN(val)) {
                $storeField.hide();
                $discountField.hide();
                return;
            }

            $storeField.toggle([3, 5].includes(val));
            $discountField.toggle(val === 5);
        }

        // Update when role changes
        $role.on('change', updateEditFields);

        // When modal shown, run update (small timeout to allow async population)
        $('#employee-edit').on('shown.bs.modal', function () {
            // If you populate the form via AJAX, ensure you call .trigger('change') after setting the role.
            setTimeout(updateEditFields, 30);
        });

        // Defensive: update immediately on page load (if modal content is already present)
        updateEditFields();
    });



    $(document).ready(function () {
        function toggleViewStoreField() {
            const selectedRoleView = parseInt($("#role-view").val());
            const storeFieldView = $("#store-field-view");
            if ([3, 5].includes(selectedRoleView)) {
                storeFieldView.show();
            } else {
                storeFieldView.hide();
            }
        }

        // On role change in edit modal
        $(document).on('change', "#role-edit", toggleViewStoreField);

        // When opening the modal, after setting the role value, call the toggle
        $('#employee-view').on('shown.bs.modal', function () {
            toggleViewStoreField();
        });

    });
</script>
<script>
    $(document).on('change', '.status-toggle', function () {
        var checkbox = $(this);
        var userId = checkbox.data('id');
        var newStatus = checkbox.is(':checked') ? 1 : 0;
        var actionText = newStatus ? 'activate' : 'deactivate';

        // Revert toggle until confirmed
        checkbox.prop('checked', !newStatus);

        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to ${actionText} this employee?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, ' + actionText,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Proceed with AJAX status update
                $.ajax({
                    url: "{{route('admin.change-status')}}", // Adjust route as needed
                    method: 'POST',
                    data: {
                        id: userId,
                        status: newStatus,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        if (res.success) {
                            Swal.fire('Success!', res.message, 'success');
                            checkbox.prop('checked', newStatus);
                            // Add this line to reload DataTable:
                            $('#employee-list-table').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error!', res.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    });
</script>
<script>
    $(document).on('change', '.allow-all', function () {
        const module = $(this).data('module');
        const checked = $(this).is(':checked');
        $('.' + module + '-perm').prop('checked', checked);
    });
</script>

<!-- <script>
    $(document).on('click', '[data-bs-target="#permissionModal"]', function () {
        const userId = $(this).data('id');
        $('#permission_user_id').val(userId);

        $.get('admin/users/' + userId + '/permissions', function (res) {
            let html = '';

            for (const module in res.permissions) {
                const perms = res.permissions[module];
                let hasAnyPermission = false;

                html += `<tr><td class="text-capitalize">${module}</td>`;

                ['create', 'update', 'delete'].forEach(action => {
                    const match = perms.find(p => p.action === action);

                    if (match) {
                        hasAnyPermission = true;
                        const isChecked = res.user_permissions.includes(Number(match.id));
                        html += `<td class="text-center">
                                <input type="checkbox" name="permissions[]" value="${match.id}"
                                    class="perm-checkbox ${module}-perm" ${isChecked ? 'checked' : ''}>
                             </td>`;
                    } else {
                        html += `<td></td>`; // Empty cell if not available in DB
                    }
                });

                // Only show "Allow All" if at least one permission exists
                if (hasAnyPermission) {
                    html += `<td class="text-center">
                            <input type="checkbox" class="allow-all" data-module="${module}">
                         </td>`;
                } else {
                    html += `<td></td>`;
                }

                html += `</tr>`;
            }

            $('#permissionTableBody').html(html);
        });
    });
</script> -->
<script>
    $(document).on('click', '[data-bs-target="#permissionModal"]', function () {
        const userId = $(this).data('id');
        $('#permission_user_id').val(userId);

        $.get('admin/users/' + userId + '/permissions', function (res) {
            let html = '';
            const hiddenModules = ['user', 'permission', 'store', 'user_status'];

            for (const module in res.permissions) {
                if (res.role_id == 5 && hiddenModules.includes(module.toLowerCase())) {
                    continue;
                }

                const perms = res.permissions[module];
                html += `<tr><td class="text-capitalize">${module}</td>`;

                ['create', 'update', 'delete'].forEach(action => {
                    const match = perms.find(p => p.action === action);
                    const isChecked = match && res.user_permissions.includes(Number(match.id));

                    html += `<td class="text-center">
                ${match ? `<input type="checkbox" name="permissions[]" value="${match.id}"
                    class="perm-checkbox ${module}-perm" ${isChecked ? 'checked' : ''}>` : ''}
            </td>`;
                });

                html += `<td class="text-center">
            <input type="checkbox" class="allow-all" data-module="${module}">
        </td></tr>`;
            }

            $('#permissionTableBody').html(html);
        });

    });
</script>
<script>
    $('#permissionForm').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize();

        $.ajax({
            url: "{{route('admin.save-permission')}}",
            method: 'POST',
            data: formData,
            success: function (res) {
                if (res.success) {
                    $('#permissionModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Permissions updated',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update failed',
                        text: 'Could not update permissions.',
                    });
                }
            }
        });
    });

</script>
<script>
    // When opening modal, set clerk_id
    $(document).on('click', '.manageSidebarLinks', function () {
        let clerkId = $(this).data('id');
        $('#sidebar_clerk_id').val(clerkId);

        // Clear all checkboxes first
        $('#sidebarLinksForm input[type="checkbox"]').prop('checked', false);

        // Fetch existing sidebar links for this clerk
        $.ajax({
            url: '{{ route("admin.sidebar-links.fetch") }}',
            type: 'GET',
            data: { clerk_id: clerkId },
            success: function (response) {
                if (response.status === 'success' && Array.isArray(response.links)) {
                    response.links.forEach(function (linkKey) {
                        // Ensure the selector matches the checkbox IDs in your blade file
                        let checkbox = $('#link_' + linkKey);
                        if (checkbox.length) {
                            checkbox.prop('checked', true);
                        }
                    });
                }
            },
            error: function (xhr) {
                console.error('Error fetching sidebar links:', xhr.responseText);
            }
        });
    });


    // Form submit handler
    $('#sidebarLinksForm').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: '{{ route("admin.sidebar-links.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                if (response.status === 'success') {
                    showAlert('success', response.message);
                    $('#sidebarLinksModal').modal('hide');
                } else {
                    showAlert('danger', response.message || 'An unexpected error occurred.');
                }
            },
            error: function (xhr) {
                let errMsg = 'Something went wrong.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                showAlert('danger', errMsg);
            }
        });
    });

    // Bootstrap alert helper
    function showAlert(type, message) {
        let alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('.page-header').after(alertHtml);
    }
</script>


<style>
    /* Add to your CSS */
    .switch {
        position: relative;
        display: inline-block;
        width: 38px;
        height: 22px;
        vertical-align: middle;
    }

    .switch input {
        display: none;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #e95d5d;
        transition: .4s;
        border-radius: 22px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #28a745;
    }

    input:checked+.slider:before {
        transform: translateX(16px);
    }
</style>
@endsection