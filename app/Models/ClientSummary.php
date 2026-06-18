<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientSummary extends Model {
    protected $table = 'client_summaries';

    protected $fillable = [
        'client_name', 'email', 'campaigns_count', 'shares_count', 
        'form_submissions_count', 'credits_consumed'
    ];
}
