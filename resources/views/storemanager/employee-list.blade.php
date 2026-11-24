@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Store Employees</h4>
                    <h6>Manage your store employees</h6>
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
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#add-employee"><i
                        data-feather="plus-circle" class="me-2"></i>Add New
                    Employee</a>
            </div>
            @endif
        </div>

        <div class="card table-list-card">
            <div class="card-body pb-0">

                <div class="table-top table-top-two table-top-new">
                    <div class="search-set mb-0">
                        <div class="total-employees mx-4">
                            <h6>
                                <i data-feather="users" class="feather-user"></i>Total Employees <span>{{ $userCount
                                    }}</span>
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
                                        <!-- DataTable input will be moved here -->
                                        <a href="#" class="btn btn-searchset">
                                            <i data-feather="search" class="feather-search"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-12">

                                <div class="input-blocks">
                                    <i data-feather="stop-circle" class="info-img"></i>
                                    <select class="select" id="status-filter">
                                        <option value="">Choose Status</option>
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
                    <table class="table datanew" id="store-employee-table">
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
                            <h4>Edit Employee Details</h4>
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
                                        <input type="text" class="form-control" placeholder="Username"
                                            name="username" />
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
                                <!-- <div id="password-field" class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">New Password (Optional)</label>
                                        <input type="text" class="form-control" placeholder="Enter New Password"
                                            name="password" />

                                        <span class="text-danger error-password"></span>
                                    </div>
                                </div> -->
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
                                            <option value="" selected>
                                                Select Nationality
                                            </option>
                                            <option value="UK">
                                                United Kingdom
                                            </option>
                                            <option value="India">India</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="input-blocks">
                                        <label class="form-label">Joining Date</label>
                                        <div class="input-groupicon calender-input">
                                            <i data-feather="calendar" class="info-img"></i>
                                            <input type="text" class="datetimepicker form-control"
                                                placeholder="Select Date" name="joining_date" />
                                            <span class="text-danger error-joining_date"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select name="role_id" id="role_id" class="form-select">
                                            <option value="6">
                                                Staff
                                            </option>

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
                                                        <option value="UK">
                                                            United Kingdom
                                                        </option>
                                                        <option value="USA">
                                                            USA
                                                        </option>
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
                            <h4>View Employee</h4>
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
                                            <option value="UK">
                                                United Kingdom
                                            </option>
                                            <option value="India">India</option>
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
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select name="role_id" id="role_id" class="form-select">
                                            @foreach($roles as $role)
                                            <option value="{{ $role->id }}">
                                                {{ $role->name }}
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
                                                        <option value="UK">
                                                            United Kingdom
                                                        </option>
                                                        <option value="USA">
                                                            USA
                                                        </option>
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
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" placeholder="Email" name="email" />
                                        <span class="text-danger error-email"></span>
                                    </div>
                                </div>
                                <!-- <div id="password-field" class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="text" class="form-control" placeholder="Password"
                                            name="password" />

                                        <span class="text-danger error-password"></span>
                                    </div>
                                </div> -->
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
                                            <option value="select_gender">
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
                                            <option>
                                                Select Nationality
                                            </option>
                                            <option value="UK">
                                                United Kingdom
                                            </option>
                                            <option value="India">India</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select name="role_id" id="role_id" class="form-select">

                                            <option value="6" selected>
                                                Staff
                                            </option>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="input-blocks">
                                        <label class="form-label">Joining Date</label>
                                        <div class="input-groupicon calender-input">
                                            <i data-feather="calendar" class="info-img"></i>
                                            <input type="text" class="datetimepicker form-control"
                                                placeholder="Select Date" name="joining_date" />
                                            <span class="text-danger error-joining_date"></span>
                                        </div>
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
                                                        <option>
                                                            Select Nationality
                                                        </option>
                                                        <option value="UK">
                                                            United Kingdom
                                                        </option>
                                                        <option value="USA">
                                                            USA
                                                        </option>
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

<!-- Permission Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header custom-modal-header">
                <h5 class="modal-title">Manage Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="permissionForm">
                <input type="hidden" name="storeUser_id" id="permission_user_id">
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
                                <tr>
                                    <td class="text-capitalize">{{ $module }}</td>

                                    @foreach(['create', 'update', 'delete'] as $action)
                                    @php
                                    $permExists = $actions->firstWhere('action', $action);
                                    @endphp
                                    <td class="text-center">
                                        <input type="checkbox" name="permissions[]"
                                            value="{{ $permExists ? $permExists->id : '' }}"
                                            class="perm-checkbox {{ $module }}-perm" {{ $permExists ? '' : 'disabled'
                                            }}>
                                    </td>
                                    @endforeach

                                    <td class="text-center">
                                        <input type="checkbox" class="allow-all" data-module="{{ $module }}">
                                    </td>
                                </tr>
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

@endsection @section('scripts')
<script>
    $(document).ready(function () {
        let table = $('#store-employee-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.employee-list') }}",
                data: function (d) {
                    d.status = $('#status-filter').val();
                    d.order_type = $('#sort-order').val();
                }
            },
            columns: [
                { data: 'staff_info', name: 'name' },
                { data: 'emp_code', name: 'emp_code' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            pageLength: 10,
            drawCallback: function () {
                feather.replace();
            },
            initComplete: function () {
                let searchWrapper = $('#store-employee-table_filter');
                let defaultSearchBox = searchWrapper.find('input');
                $('#custom-search').append(defaultSearchBox);
                defaultSearchBox.attr('placeholder', 'Search Employees...');

                searchWrapper.remove();
            }

        });

        // Reload on status filter change
        $('#status-filter').on('change', function () {
            table.ajax.reload();
        });
        $('#sort-order').on('change', function () {
            table.ajax.reload();
        });
    });

</script>
<script>
    $(document).ready(function () {
        if ($.fn.DataTable.isDataTable(".users-table")) {
            $(".users-table").DataTable().destroy();
        }
        if ($.fn.DataTable.isDataTable(".permissions-table")) {
            $(".permissions-table").DataTable().destroy();
        }

        // View Employee with AJAX
        $(document).on('click', '.viewEmployee', function () {
            var empId = $(this).data("id");

            $.ajax({
                url: "{{ route('admin.view-staff') }}",
                type: "GET",
                data: {
                    empId: empId,
                },
                success: function (response) {
                    console.log(response);

                    if (response.success) {
                        console.log(response);
                        $('#employee-view img[name="file"]').attr(
                            "src",
                            "/storage/" + response.data.profile_image
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
                error: function (xhr, status, error) {
                    console.error("Error:", xhr.responseText);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to View/Find Employee. Please try again.",
                        confirmButtonColor: "#d33",
                    });
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
                url: "{{ route('admin.edit-staff') }}",
                type: "GET",
                data: {
                    empId: empId,
                },
                success: function (response) {
                    if (response.success) {
                        // console.log(response);
                        $("#employee-edit").on("shown.bs.modal", function () {
                            $('#employee-edit img[name="file"]').attr(
                                "src",
                                "/storage/" + response.data.profile_image
                            );
                            $("#employee-edit")
                                .find('input[name="id"]')
                                .val(response.data.id || "");
                            $("#employee-edit")
                                .find('input[name="first_name"]')
                                .val(response.data.first_name || "");
                            $("#employee-edit")
                                .find('input[name="last_name"]')
                                .val(response.data.last_name || "");
                            $("#employee-edit")
                                .find('input[name="username"]')
                                .val(response.data.username || "");
                            $("#employee-edit")
                                .find('input[name="email"]')
                                .val(response.data.email || "");
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

                            $("#employee-edit").modal("show");
                        });

                        $("#employee-edit").modal("show");
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Error loading employee data!",
                            confirmButtonColor: "#d33",
                        });
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
                url: "{{ route('admin.update-staff') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: "Employee updated successfully!",
                            confirmButtonColor: "#3085d6",
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
                url: "{{route('admin.add-store-staff')  }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: response.message,
                            confirmButtonColor: "#3085d6",
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



    // Delete employee

    $(document).on("click", ".deleteStaff", function (e) {
        e.preventDefault();
        const empId = $(this).data("id");
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
            confirmButtonClass: "btn btn-primary",
            cancelButtonClass: "btn btn-danger ml-1",
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                });

                $.ajax({
                    url: "{{ route('admin.delete-staff') }}",
                    type: "DELETE",
                    data: {
                        empId: empId,
                        _token: "{{ csrf_token() }}", // ✅ Include this line
                    },
                    success: function (response) {
                        if (response.success) {
                            console.log(response);
                            Swal.fire({
                                icon: "success",
                                title: "Deleted!",
                                text: "Employee has been deleted successfully.",
                                confirmButtonClass: "btn btn-success",
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: "Error deleting employee!",
                                confirmButtonClass: "btn btn-danger",
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: "Error deleting employee!",
                            confirmButtonClass: "btn btn-danger",
                        });
                        console.log(xhr.responseText);
                    },
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: "Cancelled",
                    text: "The employee record is safe :)",
                    icon: "error",
                    confirmButtonClass: "btn btn-success",
                });
            }
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
                    url: "{{route('admin.change-status-storeEmployee')}}", // Adjust route as needed
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
                            $('#store-employee-table').DataTable().ajax.reload(null, false);
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
<script>
    $(document).on('click', '[data-bs-target="#permissionModal"]', function () {
        const storeUserId = $(this).data('id');
        $('#permission_user_id').val(storeUserId);
        $.get('admin/users/' + storeUserId + '/permissions', function (res) {
            let html = '';
            for (const module in res.permissions) {
                const perms = res.permissions[module];
                html += `<tr><td class="text-capitalize">${module}</td>`;
                ['create', 'update', 'delete'].forEach(action => {
                    const match = perms.find(p => p.action === action);
                    const isChecked = match && res.user_permissions.includes(Number(match.id));

                    html += `<td class="text-center">
                            <input type="checkbox" name="permissions[]" value="${match?.id ?? ''}"
                                class="perm-checkbox ${module}-perm" ${isChecked ? 'checked' : ''}>
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