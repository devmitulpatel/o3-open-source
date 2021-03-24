<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MS\Core\Traits\Models\ModelAction;

class Company extends Model
{
    use HasFactory, ModelAction;

    protected $fillable = [
        'official_name',
        'short_name'
    ];

    public static function do_when_created($collection)
    {
        CompanyLedger::create(
            ['name' => 'master', 'current_balance' => 0, 'company_id' => $collection->id, 'type_id' => 1, 'status' => 1]
        );
        CompanyLedger::create(
            ['name' => 'cash', 'current_balance' => 0, 'company_id' => $collection->id, 'type_id' => 2, 'status' => 1]
        );
    }

    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}
