<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
      'product_id',
      'sale_return_id',
      'product_name',
      'price',
      'selling_price',
      'gst',
      'gst_percentage',
      'hsn',
      'code',
      'quantity',
      'discount',
      'cost',
      'subtotal',
      'batch',
      'expiry_date',
        'unit'
  ];
}
