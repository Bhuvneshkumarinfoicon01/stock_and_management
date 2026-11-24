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

        <form
            id="profileForm"
            action="{{ route('postProfile') }}"
            method="post"
            enctype="multipart/form-data"
        >
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="profile-set">
                        <div class="profile-head"></div>
                        <div class="profile-top">
                            <div class="profile-content">
                                <div class="profile-contentimg">
                                    <img
                                    src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('admin\assets\img\avatar\default-avatar.png') }}"
                                        alt="img"
                                        id="blah"
                                        name="profile_image"
                                        class="img-thumbnail"
                                    />
                                    <div class="profileupload">
                                        <input
                                            name="file"
                                            type="file"
                                            id="imgInp"
                                        />
                                        <a href="javascript:void(0);">
                                            <img
                                                src="\admin\assets\img\icons\edit-set.svg"
                                                alt="img"
                                            />
                                        </a>
                                    </div>

                                    {{-- Show error for profile image --}}
                                    @error('file')
                                    <div class="text-danger mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="profile-contentname">
                                    <h2>{{$user->username}}</h2>
                                    <h4>
                                        Updates Your Photo and Personal Details.
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-3 col-lg-4 col-md-3">
                            <div class="input-blocks">
                                <label class="form-label">First Name</label>
                                <input
                                    name="firstName"
                                    type="text"
                                    class="form-control"
                                    placeholder="Enter First Name"
                                    value="{{ old('firstName', $user->first_name) }}"
                                />
                                @error('firstName')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-3">
                            <div class="input-blocks">
                                <label class="form-label">Last Name</label>
                                <input
                                    name="lastName"
                                    value="{{ old('lastName', $user->last_name) }}"
                                    type="text"
                                    class="form-control"
                                    placeholder="Enter Last Name"
                                />
                                @error('lastName')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-3">
                            <div class="input-blocks">
                                <label>Email</label>
                                <input
                                    name="email"
                                    value="{{ old('email', $user->email) }}"
                                    type="email"
                                    class="form-control"
                                    placeholder="Enter Email Address"
                                />
                                @error('email')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-3">
                            <div class="input-blocks">
                                <label class="form-label">Phone</label>
                                <input
                                    name="phone"
                                    value="{{ old('phone', $user->phone) }}"
                                    class="form-control"
                                    type="text"
                                    placeholder="Enter Phone no."
                                />
                                @error('phone')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-3">
                            <div class="input-blocks">
                                <label class="form-label">User Name</label>
                                <input
                                    name="username"
                                    value="{{ old('username', $user->username) }}"
                                    type="text"
                                    class="form-control"
                                    placeholder="Enter Username"
                                />
                                @error('username')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- <div class="col-xl-3 col-lg-4 col-md-3">
                            <div class="input-blocks">
                                <label class="form-label">Password</label>
                                <div class="pass-group">
                                    <input
                                        name="password"
                                        type="password"
                                        class="pass-input form-control"
                                    />
                                    <span
                                        class="fas toggle-password fa-eye-slash"
                                    ></span>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <div class="card-title-head">
                        <h6>
                            <span
                                ><i
                                    data-feather="map-pin"
                                    class="feather-chevron-up"
                                ></i></span
                            >Our Address
                        </h6>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input
                                    name="address"
                                    value="{{ old('address', $user->address) }}"
                                    type="text"
                                    class="form-control"
                                    placeholder="Enter Address..."
                                />
                                @error('address')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <input
                                    name="country"
                                    value="{{ old('country', $user->country) }}"
                                    type="text"
                                    class="form-control"
                                    placeholder="Enter Country..."
                                />
                                @error('country')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-3">
                            <div class="mb-3">
                                <label class="form-label"
                                    >State / Province</label
                                >
                                <input
                                    name="state"
                                    value="{{ old('state', $user->state) }}"
                                    type="text"
                                    class="form-control"
                                    placeholder="Enter State/Province..."
                                />
                                @error('state')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-3">
                            <div class="mb-3">
                                <label class="form-label">City</label>
                                <input
                                    name="city"
                                    value="{{ old('city', $user->city) }}"
                                    type="text"
                                    class="form-control"
                                    placeholder="Enter City..."
                                />
                                @error('city')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Postal Code</label>
                                <input
                                    name="zipcode"
                                    value="{{ old('zipcode', $user->zipcode) }}"
                                    type="text"
                                    class="form-control"
                                    placeholder="Enter Postal Code..."
                                />
                                @error('zipcode')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <button class="btn btn-submit me-2" type="submit">
                                submit
                            </button>

                            <a href="/" class="btn btn-cancel"
                                >Cancel</a
                            >
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- /product list -->
    </div>
</div>

@endsection @section('scripts')

<script>
    $(document).on("submit", "#profileForm", function (e) {
        e.preventDefault(); // Prevent default form submission

        let formData = new FormData(this);

        // Check if file is selected
        var fileInput = $('input[name="file"]')[0];
        if (fileInput.files.length === 0) {
            var existingProfileImage = $("#profileForm")
                .find("img[name='profile_image']")
                .attr("src");

            // Append existing profile image if no new file is uploaded
            if (existingProfileImage) {
                formData.append("existing_profile_image", existingProfileImage);
            }
        }

        // Log FormData content to debug
        for (let pair of formData.entries()) {
            console.log(pair[0] + ": " + pair[1]);
        }

        $.ajax({
            url: "{{ route('postProfile') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    $(".text-danger").html("");
                    $(".form-control").removeClass("is-invalid");
                    Swal.fire({
                        icon: "success",
                        title: "Profile Updated!",
                        text: response.message,
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: response.message,
                    });
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;

                    // Remove all previous error messages
                    $(".text-danger").remove();
                    $(".form-control").removeClass("is-invalid");

                    // Loop through each error and show it below the respective field
                    $.each(errors, function (key, value) {
                        let inputField = $("[name='" + key + "']");
                        if (inputField.length) {
                            inputField.addClass('is-invalid');
                            inputField.closest('.input-blocks, .mb-3').append('<div class="text-danger">' + value[0] + "</div>");
                        }
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "An error occurred while updating the profile.",
                    });
                }
            },
        });
    });

    // Image Preview on File Select
    // $("#imgInp").change(function () {
    //     let reader = new FileReader();
    //     reader.onload = (e) => {
    //         $("#blah").attr("src", e.target.result);
    //     };
    //     reader.readAsDataURL(this.files[0]);
    // });
</script>
@endsection
