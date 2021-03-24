<?php


namespace MS\Core\Traits\Migrations;



use App\Models\LedgerType;
use App\Models\TransactionType;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MS\Core\Traits\Models\HasRoles;

trait LedgerSystem
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('current_balance')->default(0);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('type_id');
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        Schema::create('company_ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('current_balance')->default(0);
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('type_id');
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        Schema::create('ledger_user', function (Blueprint $table) {
            $table->unsignedBigInteger('ledger_id');
            $table->unsignedBigInteger('user_id');
        });
        Schema::create('ledger_types',function(Blueprint $table){
            $table->id();
            $table->string('name');
        });
        Schema::create('transaction_types',function(Blueprint $table){
            $table->id();
            $table->string('name');
        });
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('testing');
            $table->unsignedBigInteger('ledger_id')->default(0);
            $table->unsignedBigInteger('type_id')->default(0);
            $table->bigInteger('before_balance');
            $table->bigInteger('after_balance');
            $table->longText('extra');
            $table->integer('status')->default(0);
            $table->timestamps();
        }
        );
        Schema::create(
            'ledger_transaction',
            function (Blueprint $table) {
                $table->unsignedBigInteger('ledger_id');
                $table->unsignedBigInteger('transaction_id');
            //    $table->timestamps();
            }
        );
//        Schema::create('ledger_user', function (Blueprint $table) {
//            $table->unsignedBigInteger('ledger_id');
//            $table->unsignedBigInteger('user_id');
//        });
        LedgerType::create(['name' => 'Master']);
        LedgerType::create(['name' => 'Cash']);
        LedgerType::create(['name' => 'Bank Account']);
        TransactionType::create(['name' => 'credit']);
        TransactionType::create(['name' => 'debit']);
//        Ledger::create(['name'=>'master','current_balance'=>0,'user_id'=>1,'type_id'=>1,'status'=>1]);
//        Ledger::create(['name'=>'cash','curre   nt_balance'=>0,'user_id'=>1,'type_id'=>2,'status'=>1]);
        HasRoles::SeedForRoles();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ledger');
        Schema::dropIfExists('transaction');
        Schema::dropIfExists('ledger_transaction');
        Schema::dropIfExists('ledger_user');
        Schema::dropIfExists('ledger_types');
        Schema::dropIfExists('transactions_types');
    }
}
