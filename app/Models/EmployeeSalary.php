<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{


  protected $fillable = [
'employee_id',
'salary',
'actual_salary',
'from',
'to',
'advance',
'salary_frequency',
'salary_calculation',
'user_id',
'user_name',
'employee_name',
'note',
'incentive',
'account_id',
'account_name',
'created_at',
'updated_at',


    ];

}
