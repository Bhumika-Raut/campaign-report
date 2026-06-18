<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model {
    use HasFactory;

    protected $fillable = [
        'id', 'email', 'company', 'account_created', 'last_created_post', 
        'plan', 'status', 'consumed', 'remaining', 'last_shared', 
        'clicks', 'posts', 'comments'
    ];

    protected $casts = [
        'account_created' => 'date',
        'last_created_post' => 'date',
        'last_shared' => 'date',
    ];
}
