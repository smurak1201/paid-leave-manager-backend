<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveUsage extends Model
{
    protected $fillable = [
        'employee_id',
        'used_date',
    ];
}
