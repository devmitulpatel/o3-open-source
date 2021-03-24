<?php


namespace MS\Core\Traits\Models;


use App\Models\Company;

trait AttachCompany
{


    public function company(){
        return $this->belongsToMany(Company::class);
    }

}
