<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Kitchen extends Model
{

  protected $fillable = [
        'name',
        'code',
        'location_id',
        'created_by',
        'updated_by',
        'kot_printer',

    ];

    public function location()
    {
      return $this->belongsTo(Location::class);
    }

      /*public function products()
      {
        return $this->belongsToMany(Product::class)->withPivot('quantity','supplier_id','batch','code','expiry_date','unit','cost','cost_include_gst','price','price_include_gst','sale_price')->withTimestamps();
      }*/

}
