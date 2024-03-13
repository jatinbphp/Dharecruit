<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePOCTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_o_c_transfers', function (Blueprint $table) {
            $table->id();
            $table->integer('pv_company_id')->nullable();
            $table->integer('transfer_by')->nullable();
            $table->integer('transfer_to')->nullable();
            $table->integer('transfer_type')->default(2)->comment('1 = Key, 2 = Automatic, 3 = Self');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('p_o_c_transfers');
    }
}
