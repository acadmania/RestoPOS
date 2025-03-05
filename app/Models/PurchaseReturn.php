<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory;


    public function items()
      {
        return $this->hasmany(PurchaseReturnItem::class);
      }
}
