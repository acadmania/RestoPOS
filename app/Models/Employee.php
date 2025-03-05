<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{



  protected $fillable = [
  'name',
  'code',
  'employee_category_id',
  'kitchen_id',
  'waiter',
'login',
'address',
'area',
'district',
'phone',
'salary',
'salary_frequency',
  'user_id',
  'user_name',


    ];
    public function salaries()
{
return $this->hasmany(EmployeeSalary::class);
}
}
