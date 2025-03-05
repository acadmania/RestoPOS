<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
  protected $casts = [
      'extras' => 'array'
  ];

  protected $fillable = [
        'name',
        'code',
        'balance',
        'created_by',
        'updated_by',

    ];

}
