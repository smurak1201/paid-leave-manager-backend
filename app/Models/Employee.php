<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';

    protected $fillable = [
        'employee_id',
        'last_name',
        'first_name',
        'joined_at',
    ];
}
