<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
  protected $casts = [
      'extras' => 'array'
  ];

  protected $fillable = [
        'name',
        'customer_group_id',
        'phone',
        'address',
        'area',
        'district',
        'no_points',
        'ppo',
        'monthly_limit',
        'yearly_limit',

    ];

    public function points()
    {
      return $this->hasmany(Point::class);
    }
    public function sales()
    {
      return $this->hasmany(Sale::class);
    }
}
