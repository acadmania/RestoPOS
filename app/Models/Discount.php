<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;
    protected $casts = [
        'excluded_products' => 'array',
        'excluded_categories' => 'array',
    ];

    protected $fillable = [
          'title',
          'type',
          'value_type',
          'minimum',
          'maximum',
          'maximum',
          'value',
          'active',
          'excluded_products',
          'excluded_categories',
      ];
}
