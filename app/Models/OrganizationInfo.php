<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationInfo extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'website',
        'logo_path',
    ];
}
