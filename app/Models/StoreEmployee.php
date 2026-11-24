<?php

namespace App\Models;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreEmployee extends Model
{
    use HasFactory;

    protected $table = 'store_employees';

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'username',
        'email',
        'role_id',
        'store_id',
        'password',
        'profile_image',
        'phone',
        'emp_code',
        'dob',
        'gender',
        'nationality',
        'joining_date',
        'emergency_no1',
        'emergency_no2',
        'address',
        'city',
        'state',
        'country',
        'zipcode',
        'status',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'store_employee_permissions', 'store_employee_id', 'permission_id');
    }
}
