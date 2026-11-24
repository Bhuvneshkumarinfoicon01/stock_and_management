<?php

namespace App\Http\Controllers\admin;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function getuser(Request $request)
    {
        $roles = Role::all();
        $authUser = auth()->user();
        $countryList = config('countries');
        $permissions = Permission::all()->groupBy('module');
        if ($request->ajax()) {
            $query = User::query()->orderBy('role_id', 'asc');

            // Filter users based on authenticated user's role
            if ($authUser->role_id == 2) {
                $query->whereIn('role_id', [3, 4, 5]);
            }
            if ($authUser->role_id == 3) {
                $query->whereIn('role_id', [5]);
            }

            if ($request->has('status') && $request->status !== 'choose') {
                $query->where('status', $request->status);
            }
            if ($request->has('order_type') && in_array($request->order_type, ['asc', 'desc'])) {
                $query->orderBy('created_at', $request->order_type);
            }

            return datatables()
                ->of($query)
                ->addColumn('user_profile', function ($query) {
                    $image = $query->profile_image
                        ? asset('storage/' . $query->profile_image)
                        : asset('admin/assets/img/avatar/default-avatar.png');
                    return '
                    <div class="userimgname">
                        <a href="javascript:void(0);" class="product-img">
                            <img src="' . $image . '" alt="product" />
                        </a>
                        <div>
                            <a href="javascript:void(0);">' . e($query->name) . '</a>
                            <span class="emp-team">' . e($query->role->name ?? '-') . '</span>
                        </div>
                    </div>
                ';
                })
                ->addColumn('user_id', fn($row) => $row->id)
                ->addColumn('email', fn($row) => $row->email)
                ->addColumn('phone', fn($row) => $row->phone)
                ->addColumn('status_badge', function ($query) {
                    $status = $query->status == 1 ? 'Active' : 'Inactive';
                    $class = $query->status == 1 ? 'badge-linesuccess' : 'badge-linedanger';

                    return '<span class="badge ' . $class . '">' . $status . '</span>';
                })
                ->addColumn('actions', function ($user) use ($authUser) {
                    $checked = $user->status == 1 ? 'checked' : '';
                    $actions = '<div class="edit-delete-action">';
                    $actions .= '
            <a class="me-2 p-2 viewEmployee" href="#" data-id="' . $user->id . '" data-bs-toggle="modal" data-bs-target="#employee-view">
                <i data-feather="eye" class="feather-eye"></i>
            </a>';

                    if ((int) $authUser->role_id === 1 && (int) $user->id !== (int) $authUser->id) {
                        $actions .= '
            <a class="me-2 p-2 resetUserPassword" href="javascript:void(0);" data-id="' . $user->id . '" data-name="' . e($user->name) . '" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" title="Reset password">
                <i data-feather="key" class="feather-key"></i>
            </a>';
                    }

                    if (hasPermission('update', 'user')) {
                        $actions .= '
            <a class="me-2 p-2 editEmployee" href="javascript:void(0);" data-id="' . $user->id . '" data-bs-toggle="modal" data-bs-target="#employee-edit">
                <i data-feather="edit" class="feather-edit"></i>
            </a>';
                    }

                    if (hasPermission('create', 'permission')) {
                        $actions .= '
            <a class="me-2 p-2" href="javascript:void(0);" data-id="' . $user->id . '" data-bs-toggle="modal" data-bs-target="#permissionModal">
                <i data-feather="shield" class="feather-shield"></i>
            </a>';
                    }
                    if ($user->role_id != 1) {
                        if (hasPermission('update', 'user_status')) {
                            $actions .= '
            <label class="switch">
                <input type="checkbox" class="status-toggle" data-id="' . $user->id . '" ' . $checked . '>
                <span class="slider round"></span>
            </label>';
                        }
                    }
                    if ($user->role_id != 1) {
                        if (hasPermission('delete', 'user')) {
                            $actions .= '
            <a class="confirm-text p-2 deleteEmployee" href="javascript:void(0);" data-id="' . $user->id . '">
                <i data-feather="trash-2" class="feather-trash-2"></i>
            </a>';
                        }
                    }

                    // 🔹 New: Manage Sidebar Links (Only for Clerks)
                    if ($user->role_id == 5) {
                        $actions .= '
        <a class="me-2 p-2 manageSidebarLinks" href="javascript:void(0);" data-id="' . $user->id . '" data-bs-toggle="modal" data-bs-target="#sidebarLinksModal" title="Assign Sidebar Links">
            <i data-feather="list" class="feather-list"></i>
        </a>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['user_profile', 'status_badge', 'actions'])
                ->make(true);
        }

        return view('admin.employee-list', compact('roles', 'countryList', 'permissions'));
    }

    // public function getjsondata()
    // {
    //     $users = User::select(['id', 'name', 'email', 'phone', 'status'])
    //         ->orderBy('id', 'desc')
    //         ->get();
    //     return response()->json([
    //         'data' => $users,
    //     ]);
    // }

    public function viewEmployee(Request $request)  // VIEW EMPLOYEE FUNCTION
    {
        $employee = User::find($request->empId);
        if ($employee) {
            return response()->json([
                'success' => true,
                'data' => $employee,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found!',
            ]);
        }
    }

    // ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // EDIT AND UPDATE FUNCTION (EMPLOYEE SECTION)
    public function edit($id)  // EDIT EMPLOYEE FUNCTION
    {
        $employee = User::find($id);
        if (!hasPermission('update', 'user')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found!']);
        }

        $roles = Role::all();
        $stores = Store::all();

        return response()->json([
            'success' => true,
            'data' => $employee,
            'roles' => $roles,
            'stores' => $stores,
        ]);
    }

    public function update(Request $request)  // UPDATE EMPLOYEE FUNCTION
    {
        // dd($request->all());
        if (!hasPermission('update', 'user')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }
        $user = User::findOrFail($request->id);
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|unique:users,username,' . $user->id,
            'role_id' => 'required',
            'phone' => 'required|regex:/^\+?[0-9]{10,15}$/|unique:users,phone,' . $user->id,
            'emp_code' => 'required|string|max:50|unique:users,emp_code,' . $user->id,
            'joining_date' => 'required|date|before_or_equal:today',
        ];

        // Conditionally require role_id if not 1 or 2
        if (!in_array((int) $request->role_id, [1, 2])) {
            $rules['store'] = 'required';
        }

        if (in_array((int) $request->role_id, [5])) {
            $rules['store'] = 'required';
            $rules['discount_percentage'] = 'required';
        }

        $request->validate($rules, [
            'store.required' => 'Select store you want to assign the employee',
            'discount_percentage.required' => 'Max discount percentage is required for clerks',
            'phone.regex' => 'Phone number must be 10-15 digits only',
            'phone.required' => 'The contact number is required',
            'phone.unique' => 'This contact number already exists'
        ]);
        $oldData = $user->toArray();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->username = $request->input('username');
        $user->phone = $request->input('phone');
        $user->emp_code = $request->input('emp_code');
        $user->role_id = $request->input('role_id');
        $user->gender = $request->input('gender');
        $user->nationality = $request->input('nationality');
        $user->dob = $request->input('dob');
        $user->address = $request->input('address');
        $user->country = $request->input('country');
        $user->city = $request->input('city');
        $user->state = $request->input('state');
        $user->zipcode = $request->input('zipcode');
        $user->assigned_store = $request->input('store');
        $user->max_discount = $request->input('discount_percentage', 0);
        $user->joining_date = $request->input('joining_date');
        $user->emergency_no1 = $request->input('emergency_no1');
        $user->emergency_no2 = $request->input('emergency_no2');
        $user->name = trim($request->first_name . ' ' . $request->last_name);
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads', 'public');
            $user->profile_image = $path;
        } elseif ($request->input('existing_profile_image')) {
            $user->profile_image = $user->profile_image;
        } else {
            $user->profile_image = null;
        }
        $user->save();
        storeHistory(
            'employee_form',
            'updated',
            'users',
            $user->id,
            $user->toArray(),
            $oldData
        );
        $userId = auth()->id();
        $message = 'New Employee (' . $user->name . ') has been successfully added.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);
        return response()->json(['success' => true, 'message' => 'Employee updated successfully.']);
    }

    // ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ADD EMPLOYEE FUNCTION
    public function add(Request $request)
    {
        if (!hasPermission('create', 'user')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }
        try {
            $rules = [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'username' => 'required|unique:users,username',
                'role_id' => 'required',
                'password' => 'required',
                'phone' => 'required|regex:/^\+?[0-9]{10,15}$/|unique:users,phone',
                'emp_code' => 'required|string|max:50|unique:users,emp_code',
                'joining_date' => 'required|date|before_or_equal:today',
            ];
            // Conditionally require role_id if not 1 or 2
            if (!in_array((int) $request->role_id, [1, 2])) {
                $rules['store'] = 'required';
            }

            $request->validate($rules, [
                'store.required' => 'Select store you want to assign the employee',
                'phone.regex' => 'The contact number must be 10-15 digits only',
                'phone.required' => 'The contact number is required',
                'phone.unique' => 'This contact number already exists'
            ]);

            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->emp_code = $request->emp_code;
            $user->dob = $request->dob;
            $user->gender = $request->gender;
            $user->nationality = $request->nationality;
            $user->role_id = $request->role_id;
            $user->joining_date = $request->joining_date;
            $user->emergency_no1 = $request->emergency_no1;
            $user->emergency_no2 = $request->emergency_no2;
            $user->address = $request->address;
            $user->country = $request->country;
            $user->state = $request->state;
            $user->city = $request->city;
            $user->zipcode = $request->zipcode;
            $user->password = Hash::make($request->password);
            $user->assigned_store = $request->store;
            $user->max_discount = $request->discount_percentage;

            $user->name = trim($request->first_name . ' ' . $request->last_name);
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('uploads', 'public');
                $user->profile_image = $path;
            } else {
                $user->profile_image = null;
            }

            $user->save();
            storeHistory(
                'employee_form',
                'created',
                'users',
                $user->id,
                $user->toArray(),
                null
            );
            $userId = auth()->id();
            $message = 'Employee (' . $user->name . ') has been successfully added.';
            NotificationHelper::add($userId, $message);
            NotificationHelper::pushRealtime($userId, $message);
            return response()->json([
                'success' => true,
                'message' => 'Employee added successfully!',
            ]);
        } catch (Exception $e) {
            \Log::error('Error adding employee: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding employee!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // DELETE EMPLOYEE CONTROLLER FUNCTION
    public function destroy($id)
    {
        if (!hasPermission('delete', 'user')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }
        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Employee not found!']);
        }
        $oldData = $user->toArray();
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }
        $user->delete();
        storeHistory(
            'employee_form',
            'deleted',
            'users',
            $user->id,
            null,
            $oldData
        );
        $userId = auth()->id();
        $message = 'Employee (' . $user->name . ') has been successfully deleted.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);
        return response()->json(['success' => true, 'message' => 'Employee deleted successfully!']);
    }

    // ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // PROFILE VIEW FUNCTION
    function userProfile()
    {
        return view('admin.profile');
    }

    public function getProfileInfo(Request $request)
    {
        $profile = auth()->user();
        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $profile->id,
            'phone' => 'required|numeric|digits_between:10,15',
            'username' => 'required|string|max:255|unique:users,username,' . $profile->id,
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'zipcode' => 'nullable|string|max:10',
        ]);
        $profile = User::find($profile->id);
        if ($request->hasFile('file')) {
            if ($profile->profile_image) {
                Storage::delete('public/' . $profile->profile_image);
            }
            $path = $request->file('file')->store('uploads', 'public');
            $profile->profile_image = $path;
        } elseif ($request->input('existing_profile_image')) {
            $profile->profile_image = $profile->profile_image;
        } else {
            $profile->profile_image = null;
        }
        $profile->first_name = $request->firstName;
        $profile->last_name = $request->lastName;
        $profile->username = $request->username;
        $profile->email = $request->email;
        $profile->phone = $request->phone;
        $profile->address = $request->address;
        $profile->state = $request->state;
        $profile->country = $request->country;
        $profile->city = $request->city;
        $profile->zipcode = $request->zipcode;
        $profile->role_id = auth()->user()->role_id;
        $profile->name = trim($request->firstName . ' ' . $request->lastName);
        $profile->save();
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!',
        ]);
    }

    function getData()
    {
        // $profileData = User::where('role_id', 1)->get();
        $login_user = auth()->user();
        $user = User::where('id', auth()->id())->first();
        return view('admin.profile', compact('user'));
    }

    // /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // CHNAGE PASSWORD FUNCTION
    function changePassword()
    {
        return view('admin.change-password');
    }

    function updatePassword(Request $request)
    {
        $user = User::find(auth()->id());
        if (!$user) {
            return back()->withErrors(['current_password' => 'User not found.']);
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is invalid.']);
        }

        $userId = auth()->id();
        $message = 'Your Password has been successfully updated.';
        NotificationHelper::add($userId, $message);
        NotificationHelper::pushRealtime($userId, $message);

        $user->password = Hash::make($request->new_password);
        $user->save();
        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * SuperAdmin only: set a new password for another user (no current password required).
     */
    public function resetUserPassword(Request $request, $id)
    {
        if ((int) auth()->user()->role_id !== 1) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        if ((int) $id === (int) auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Use Change Password under your profile to update your own password.',
            ], 422);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Employee not found!'], 404);
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully for ' . $user->name . '.',
        ]);
    }

    public function changeStatus(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->status = $request->status;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Employee status updated successfully!'
        ]);
    }
}
