<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    use HasFactory;
    protected $fillable = [
  'product_id',
  'purchase_return_id',
  'product_name',
  'hsn',
  'cost',
  'gst_percentage',
  'gst',
  'discount',
  'quantity',
  'subtotal',
  'batch',
  'expiry_date',
  'code',
  'unit'
    ];

}
