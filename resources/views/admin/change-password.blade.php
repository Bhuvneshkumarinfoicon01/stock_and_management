@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Profile</h4>
                <h6>User Profile</h6>
            </div>
        </div>
        <!-- /product list -->
        @if(session('success'))
        <div class="alert alert-success">
            {{ session("success") }}
        </div>
        @endif
        <form action="{{ route('update-password') }}" method="post">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div>
                        <div class="col-xl-3 col-lg-4 col-md-3">
                            <div class="input-blocks">
                                <label class="form-label"
                                    >Current Password</label
                                >
                                <div class="pass-group">
                                    <input
                                        name="current_password"
                                        type="password"
                                        class="pass-input form-control"
                                    />
                                    @error('current_password')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                    <span
                                        class="fas toggle-password fa-eye-slash"
                                    ></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-3">
                            <div class="input-blocks">
                                <label>New Password</label>
                                <div class="pass-group">
                                    <input
                                        id="new_password"
                                        name="new_password"
                                        type="password"
                                        class="pass-input form-control"
                                    />
                                    @error('new_password')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                    <span
                                        class="fas toggle-password fa-eye-slash"
                                    ></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-3">
                            <div class="input-blocks">
                                <label class="form-label"
                                    >Confirm Password</label
                                >
                                <div class="pass-group">
                                    <input
                                        id="new_password_confirmation"
                                        name="new_password_confirmation"
                                        type="password"
                                        class="pass-input form-control"
                                    />
                                    @error('new_password_confirmation')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                    <span
                                        class="fas toggle-password fa-eye-slash"
                                    ></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-submit me-2" type="submit">
                            submit
                        </button>

                        <a href="javascript:void(0);" class="btn btn-cancel"
                            >Cancel</a
                        >
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
