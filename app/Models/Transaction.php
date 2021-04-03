<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MS\Core\Traits\Migrations\LedgerSystem;

class Transaction extends Model
{
    use HasFactory, LedgerSystem;
}
