<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table_item extends Model
{
    use HasFactory;

    public function product()
  {
    return $this->belongsTo(Food::class,'food_id');
  }
}
