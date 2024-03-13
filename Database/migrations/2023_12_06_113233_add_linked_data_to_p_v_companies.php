<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLinkedDataToPVCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('p_v_companies', function (Blueprint $table) {
            $table->longText('linked_data')->nullable()->after('client_name');
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
            $table->dropColumn('linked_data');
        });
    }
}
