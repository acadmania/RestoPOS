<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCategory extends Model
{
  protected $casts = [
      'extras' => 'array'
  ];

  protected $fillable = [
        'name',


    ];
}
