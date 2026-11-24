<!-- Header -->

<div class="header">
    <!-- Logo -->
    <div class="header-left active">
        <a href="{{ url('/') }}" class="logo logo-normal">
            {{--<h6>Accounting and Stock Management System</h6>--}}
            <h6>CSL POS & Inventory Management System</h6>
        </a>
        <a href="{{ url('/') }}" class="logo logo-white">
            {{--<h6>Accounting and Stock Management System</h6>--}}
            <h6>CSL POS & Inventory Management System</h6>
        </a>
        <a href="{{ url('/') }}" class="logo-small">
            <h6>A & S</h6>
        </a>
        <a id="toggle_btn" href="javascript:void(0);">
            <i data-feather="chevrons-left" class="feather-16"></i>
        </a>
    </div>
    <!-- /Logo -->

    <a id="mobile_btn" class="mobile_btn" href="#sidebar">
        <span class="bar-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </a>

    <!-- Header Menu -->
    <ul class="nav user-menu">
        <!-- Search -->
        <li class="nav-item nav-searchinputs">
            <!-- <div class="top-nav-search">
                <a href="javascript:void(0);" class="responsive-search">
                    <i class="fa fa-search"></i>
                </a>
                <form action="#" class="dropdown">
                    <div
                        class="searchinputs dropdown-toggle"
                        id="dropdownMenuClickable"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="false"
                    >
                        <input type="text" placeholder="Search" />
                        <div class="search-addon">
                            <span
                                ><i
                                    data-feather="x-circle"
                                    class="feather-14"
                                ></i
                            ></span>
                        </div>
                    </div>
                </form>
            </div> -->
        </li>
        <!-- /Search -->
        <!-- @if ($user && in_array($user->role_id, [1, 2]))
        <li class="nav-item">
            <div class="page-header mb-0">
                <div class="page-btn">
                    <a
                        href="#"
                        class="btn btn-added"
                        data-bs-toggle="modal"
                        data-bs-target="#add-stores"
                        ><i data-feather="plus-circle" class="me-2"></i> Add
                        Store</a
                    >
                </div>
            </div>
        </li>
        @endif -->

        @if ($user && in_array($user->role_id, [1, 2]))
        <li class="nav-item">
            <div class="page-header mb-0">
                <div class="page-btn">
                    <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#add-stores"><i
                            data-feather="plus-circle" class="me-2"></i> Add Store</a>
                </div>
            </div>
        </li>
        @endif

        @php
        $user = auth()->user();
        @endphp

        @if ($user && in_array($user->role_id, [1, 2]))
        <!-- Select Store -->
        <li class="nav-item dropdown has-arrow main-drop select-store-dropdown">
            <a href="javascript:void(0);" class="dropdown-toggle nav-link select-store" data-bs-toggle="dropdown">
                <span class="user-info">
                    <span class="user-letter">
                        <img src="{{ asset('admin/assets/img/store/store-01.png') }}" alt="Store Logo"
                            class="img-fluid" />
                    </span>
                    @php
                    $storeId = session('selected_store_id');
                    $selectedStore = \App\Models\Store::find($storeId);
                    @endphp
                    <span class="user-name">
                        {{ $selectedStore ? $selectedStore->name : 'Select Store' }}
                    </span>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" id="header-store-dropdown-menu">
                @foreach($stores as $store)
                <a href="javascript:void(0);" class="dropdown-item store-select" data-id="{{ $store->id }}">
                    <img src="{{ asset('admin/assets/img/store/store-01.png') }}" class="img-fluid" />
                    {{ $store->name }}
                </a>
                @endforeach
            </div>
        </li>
        <!-- /Select Store -->
        @endif

        <!-- /Notifications -->

        <li class="nav-item dropdown nav-item-box">
            <a href="javascript:void(0);" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                <i data-feather="bell"></i>
                <span class="badge rounded-pill">0</span>
            </a>
            <div class="dropdown-menu notifications">
                <div class="topnav-dropdown-header">
                    <span class="notification-title">Notifications</span>
                    <a href="javascript:void(0)" class="clear-noti">Clear All</a>
                </div>
                <div class="noti-content">
                    <ul class="notification-list" id="notification-list">
                        <!-- Notifications will be dynamically inserted here -->
                    </ul>
                </div>
                <div class="topnav-dropdown-footer">
                    <a href="#"><i data-feather="chevrons-up" class="feather-chevrons-up"></i>Close</a>
                </div>
            </div>
        </li>

        <!-- /Notifications -->

        <!-- <li class="nav-item nav-item-box">
            <a href="#"><i data-feather="settings"></i></a>
        </li> -->

        <li class="nav-item dropdown has-arrow main-drop">
            <a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
                <span class="user-info">
                    <span class="user-letter">
                        <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('admin\assets\img\avatar\default-avatar.png') }}"
                            alt="" class="img-fluid" />
                    </span>
                    <span class="user-detail">
                        <span class="user-name">{{$user->name}}</span>
                        <span class="user-role">{{$user->role->name}}</span>
                    </span>
                </span>
            </a>
            <div class="dropdown-menu menu-drop-user">
                <div class="profilename">
                    <div class="profileset">
                        <span class="user-img"><img
                                src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('admin\assets\img\avatar\default-avatar.png') }}"
                                alt="" />
                            <span class="status online"></span></span>
                        <div class="profilesets">
                            <h6>{{$user->name}}</h6>
                            <h5>{{$user->role->name}}</h5>
                        </div>
                    </div>
                    <hr class="m-0" />
                    <a class="dropdown-item" href="{{ route('profile') }}">
                        <i class="me-2" data-feather="user"></i> My Profile</a>
                    <a class="dropdown-item" href="{{ route('change-password') }}"><i class="me-2"
                            data-feather="lock"></i>Change
                        Password</a>
                    <!-- <a class="dropdown-item" href="{{-- route('settings') --}}"
                        ><i class="me-2" data-feather="settings"></i>Settings</a
                    > -->

                    <hr class="m-0" />
                    @auth
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <img src="{{
                                    asset('admin/assets/img/icons/log-out.svg')
                                }}" class="me-2" alt="img" />
                            Logout
                        </button>
                    </form>
                    @else
                    <a class="dropdown-item" href="{{ route('login_view') }}"><img src="{{
                                asset('admin/assets/img/icons/log-out.svg')
                            }}" class="me-2" alt="img" />
                        Login</a>
                    @endauth
                </div>
            </div>
        </li>
    </ul>
    <!-- /Header Menu -->

    <!-- Mobile Menu -->
    <div class="dropdown mobile-user-menu">
        <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
            aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{-- route('profile') --}}">My Profile</a>
            <!-- <a class="dropdown-item" href="{{-- route('settings') --}}"
                >Settings</a
            > -->

            @auth
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item">Logout</button>
            </form>
            @else
            <a class="dropdown-item" href="{{ route('login_view') }}">Login</a>
            @endauth
        </div>
    </div>
    <!-- /Mobile Menu -->
</div>
<!-- /Header -->


<script>
    window.STORE_DROPDOWN_ICON = @json(asset('admin/assets/img/store/store-01.png'));

    window.appendStoreToHeaderDropdown = function (store) {
        var menu = document.getElementById('header-store-dropdown-menu');
        if (!menu || !store || store.id == null) return;
        var idStr = String(store.id);
        if (menu.querySelector('.store-select[data-id="' + idStr + '"]')) return;

        var a = document.createElement('a');
        a.href = 'javascript:void(0);';
        a.className = 'dropdown-item store-select';
        a.setAttribute('data-id', idStr);

        var img = document.createElement('img');
        img.src = window.STORE_DROPDOWN_ICON;
        img.className = 'img-fluid';
        img.alt = '';

        a.appendChild(img);
        a.appendChild(document.createTextNode(' ' + String(store.name || '').trim()));
        menu.appendChild(a);
    };

    document.addEventListener('click', function (e) {
        var item = e.target.closest('.store-select');
        if (!item || !item.closest('.select-store-dropdown')) return;

        e.preventDefault();
        var storeId = item.getAttribute('data-id');
        if (!storeId) return;

        fetch("{{ route('admin.select.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                Accept: "application/json",
            },
            body: JSON.stringify({ store_id: storeId }),
        })
            .then(function (response) {
                return response.json();
            })
            .then(function () {
                location.reload();
            })
            .catch(function (error) {
                console.error("Fetch error:", error);
            });
    });
</script>


<!-- <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script> -->
<script>
    window.NOTIFICATION_PORT = '{{ env('NOTIFICATION_PORT', 3050) }}';
    window.NOTIFICATION_SERVER_URL = '{{ config("services.notification_url") }}';
</script>
<script src="{{ asset('admin/assets/js/notification.js') }}"></script>

<script>
    window.Laravel = {
        userId: "{{ auth()->id() }}"  // Pass the authenticated user's ID here
    };
</script>