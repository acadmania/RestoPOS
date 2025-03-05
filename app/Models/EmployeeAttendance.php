<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAttendance extends Model
{


  protected $fillable = [
'employee_id',
'employee_name',
'time',
'type'


    ];

}
