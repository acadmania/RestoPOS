<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
  protected $casts = [
      'extras' => 'array'
  ];

  protected $fillable = [
    'note',
    'amount',
    'expense_category_id',
    'expense_category_name',
    'account_id',
    'account_name',
    'extras',
    'user_id',
    'user_name',
    ];

    
}
