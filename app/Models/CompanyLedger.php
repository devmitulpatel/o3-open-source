<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MS\Core\Traits\Models\AttachCompany;

class CompanyLedger extends Model
{
    use HasFactory, AttachCompany;

    protected $fillable = ['name', 'company_id', 'type_id'];

}
