<?php


namespace MS\Core\Traits\Migrations;


use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait CompanyBaseSystem
{
    public function up()
    {
        Schema::create(
            'companies',
            function (Blueprint $table) {
                $table->id();
                $table->string('official_name')->nullable();
                $table->string('short_name')->nullable();
                $table->string('tax_no')->nullable();
                $table->integer('status')->default(1);
                $table->timestamps();
            }
        );
        Schema::create(
            'company_user',
            function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('company_id');
            }
        );

        Company::create(['official_name' => 'Million Solutions LLP', 'short_name' => 'MSLLP']);
        User::first()->company()->attach(1);
    }

    public function down()
    {
        Schema::dropIfExists('company');
    }

}
