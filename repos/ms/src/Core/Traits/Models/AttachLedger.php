<?php


namespace MS\Core\Traits\Models;


use App\Models\Ledger;

trait AttachLedger
{

    public function ledger(){
        return $this->hasMany(Ledger::class);
    }


}
