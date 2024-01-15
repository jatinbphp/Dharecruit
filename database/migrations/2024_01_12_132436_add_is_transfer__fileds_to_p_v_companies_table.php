<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsTransferFiledsToPVCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('p_v_companies', function (Blueprint $table) {
            $table->integer('is_transfer')->default(0)->after('linked_data');
            $table->integer('assigned_user_id')->default(0)->after('is_transfer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('p_v_companies', function (Blueprint $table) {
            $table->dropColumn('is_transfer');
        });
    }
}
