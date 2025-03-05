<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Table extends Model
{

  protected $fillable = [
        'name',
        'code',
        'created_by',
        'updated_by',

    ];



    public function items()
      {
        return $this->hasmany(Table_item::class);
      }

}
