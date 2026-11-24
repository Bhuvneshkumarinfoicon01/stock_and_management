<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidebarLink extends Model
{
    use HasFactory;

    protected $table = 'sidebar_links';

    protected $fillable = [
        'clerk_id',
        'link_key',
        'description',
    ];

    public function clerk()
    {
        return $this->belongsTo(User::class, 'clerk_id');
    }
}
