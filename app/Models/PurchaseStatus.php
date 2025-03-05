<?php

namespace App\Models;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Model;

class PurchaseStatus extends Model
{
  public function purchase()
    {
      return $this->belongsTo(Purchase::class);
    }
}
