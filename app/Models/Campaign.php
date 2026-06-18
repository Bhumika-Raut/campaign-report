<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model {
    use HasFactory;

    protected $fillable = [
        'id', 'client_name', 'campaign_name', 'date', 'potential_reach', 
        'total_shares', 'total_clicks', 'total_comments', 'total_likes', 
        'total_posts', 'ugc_enabled', 'form_submissions'
    ];

    protected $casts = [
        'date' => 'date',
        'ugc_enabled' => 'boolean',
    ];

    public function clientSummary() {
        return $this->hasOne(ClientSummary::class, 'client_name', 'client_name');
    }
}
