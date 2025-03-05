<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
  protected $casts = [
      'extras' => 'array',
      'taxes' => 'array'
  ];

  protected $fillable = [
    'food_id',
    'sale_id',
    'food_name',
    'price',
    'selling_price',
    'gst',
    'gst_percentage',
    'hsn',

    'quantity',
    'discount',
    'cost',
    'subtotal',
];

public function product()
  {
    return $this->belongsTo(Food::class,'food_id');
  }

  public function sale()
    {
      return $this->belongsTo(Sale::class);
    }
    public function modifiers()
    {
      return $this->belongsToMany(Modifier::class);
    }
}
