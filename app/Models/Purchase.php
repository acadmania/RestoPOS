<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PurchaseItem;
use App\Models\PurchaseStatus;
class Purchase extends Model
{

  protected $casts = [
      'extras' => 'array',
      'taxes' => 'array'
  ];

  protected $fillable = [
'supplier_id',
'supplier_name',
'supplier_tax_number',
'total',
'gst',
'shipping',
'discount',
'grand_total',
'status',
'payment_status',
'account_id',
'account_name',
'location_id',
'location_name',
'user_id',
'user_name',
'extras',
'taxes',
'created_at',
'updated_at',
    ];

    public function items()
      {
        return $this->hasmany(PurchaseItem::class);
      }





      public function statuses()
  {
  return $this->hasmany(PurchaseStatus::class);
  }
      public function payStatuses()
  {
  return $this->hasmany(PurchasePayment::class);
  }
  public function location()
    {
      return $this->belongsTo(Location::class);
    }

    public function transactions()
{
return $this->hasmany(Transaction::class);
}
}
