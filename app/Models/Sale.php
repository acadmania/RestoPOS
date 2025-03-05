<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Sale extends Model
{
    protected $appends = ['serial'];

  protected $casts = [
      'extras' => 'array',
      'taxes' => 'array',
      'discounts' => 'array'
  ];

  protected $fillable = [
    'customer_id',
  'customer_name',
  'igst',
  'total',
  'taxes',
  'gst',
  'shipping',
  'discounts',
  'table_id',
  'table_name',
  'discount',
  'grand_total',
  'status',
  'payment_status',
  'location_id',
  'location_name',
  'user_id',
  'user_name',
];


public function items()
  {
    return $this->hasmany(SaleItem::class);
  }

  public function statuses()
{
return $this->hasmany(SaleStatus::class);
}
  public function payStatuses()
{
return $this->hasmany(SalePayment::class);
}
public function customer()
{
return $this->belongsTo(Customer::class);
}
public function location()
  {
    return $this->belongsTo(Location::class);
  }
  public function account()
    {
      return $this->belongsTo(Account::class);
    }

    public function getSerialAttribute()
    {
        $from = Carbon::parse($this->created_at)->startOfDay();
        $to = Carbon::parse($this->created_at)->endOfDay();


        $q = self::where('created_at', '>=', $from)->where('created_at', '<=', $to)->get();
        $i=1;
        foreach ($q as $v) {
            if($v->id == $this->id)
            {
                break;

            }
            $i++;
        }
        return $i;
    }

}
