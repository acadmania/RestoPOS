<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBrand extends Model
{
  protected $casts = [
      'extras' => 'array'
  ];

  protected $fillable = [
        'name',
        'code',
        'created_by',
        'updated_by',

    ];
}
