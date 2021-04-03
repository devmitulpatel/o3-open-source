<?php


namespace MS\Core\Traits\Migrations;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait ItemSystem
{


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'items',
            function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('price_type')->default(1);
                $table->unsignedBigInteger('unit_id')->default(1);
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('company_id');
                $table->integer('status')->default(1);
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
