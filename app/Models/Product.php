<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $casts = [

      'customer_group_discounts' => 'array'

  ];

  protected $fillable = [
        'name',
        'hsn',
        'code',
        'sellable',
        'cost',
        'cost_include_gst',
        'price',
        'price_include_gst',
        'sale_price',
        'gst_percentage',
        'product_category_id',
        'product_brand_id',
        'created_by',
        'updated_by',

    ];

    public function locations()
    {
      return $this->belongsToMany(Location::class)->withPivot('quantity','supplier_id')->withTimestamps();
    }
    public function suppliers()
    {
      return $this->belongsToMany(Supplier::class);
    }
    public function category()
    {
      return $this->belongsTo(ProductCategory::class,'product_category_id');
    }
    public function food()
    {
      return $this->belongsToMany(Food::class)->withPivot('quantity','product_name');
    }
  /*  public function purchase_unit()
    {
      return $this->belongsTo(ProductUnit::class,'purchase_unit');
    }
    public function sale_unit()
    {
      return $this->belongsTo(ProductUnit::class,'sale_unit');
    }

    public function units()
    {
      return $this->belongsToMany(ProductUnit::class,'product_unit','product_id','unit_a_id')->withPivot('unit_a_value','unit_b_id','unit_b_value');
    }*/
}
