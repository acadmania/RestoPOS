<?php

namespace App\Models;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
  protected $casts = [
      'extras' => 'array',
      'taxes' => 'array'
  ];

  protected $fillable = [
  'material_id',
  'product_id',
  'purchase_id',
  'material_name',
  'product_name',
  'cost',
  'unit',
  'sale_unit',
  'batch',
  'code',
  'expiry_date',
  'discount',
  'gst_percentage',
  'gst',
  'quantity',
  'subtotal',
  'extras',
  'created_at',
  'updated_at',

];
  public function purchase()
    {
      return $this->belongsTo(Purchase::class);
    }

    public function product()
      {
        return $this->belongsTo(Product::class);
      }


}
