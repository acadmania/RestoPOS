<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
  protected $casts = [
      'extras' => 'array'
  ];

  protected $fillable = [
        'name',
        'code',
        'tax_number',

'address',
'phone'
    ];


    public function products()
    {
      return $this->belongsToMany(Product::class)->withPivot('cost');
    }
}
