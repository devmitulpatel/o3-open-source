<?php


namespace MS\Core\Traits\Models;


use App\Models\Company;

trait AttachCompany
{


    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
