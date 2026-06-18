<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RemovedCampaign extends Model {
    use HasFactory;

    protected $fillable = [
        'id', 'client_name', 'company', 'agency', 'campaign_title', 'paid_or_not',
        'potential_reach', 'total_shares', 'reach_per_share', 'total_clicks',
        'clicks_per_share', 'total_comments', 'comments_per_share', 'total_likes',
        'likes_per_share', 'total_posts', 'reshare', 'registrations', 'emv',
        'direct_savings', 'total_return', 'roi', 'registration_per_share'
    ];

    protected $casts = [
        'potential_reach' => 'integer',
        'total_shares' => 'integer',
        'reach_per_share' => 'double',
        'total_clicks' => 'integer',
        'clicks_per_share' => 'double',
        'total_comments' => 'integer',
        'comments_per_share' => 'double',
        'total_likes' => 'integer',
        'likes_per_share' => 'double',
        'total_posts' => 'integer',
        'reshare' => 'integer',
        'registrations' => 'integer',
        'emv' => 'double',
        'direct_savings' => 'double',
        'total_return' => 'double',
        'roi' => 'double',
        'registration_per_share' => 'double',
    ];
}
