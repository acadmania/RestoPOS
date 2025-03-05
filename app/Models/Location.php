<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Location extends Model
{

  protected $fillable = [
        'name',
        'code',
        'created_by',
        'updated_by',

    ];



      public function products()
      {
        return $this->belongsToMany(Product::class)->withPivot('quantity','supplier_id','batch','code','expiry_date','unit','cost','cost_include_gst','price','price_include_gst','sale_price')->withTimestamps();
      }

}
