<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPocNameToPVCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('p_v_companies', function (Blueprint $table) {
            $table->string('poc_name')->nullable()->after('name');
            $table->string('poc_location')->nullable()->after('phone');
            $table->string('pv_company_location')->nullable()->after('poc_location');
            $table->string('client_name')->nullable()->after('pv_company_location');
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
            $table->dropColumn('poc_name');
            $table->string('poc_location');
            $table->string('pv_company_location')->nullable();
            $table->string('client_name')->nullable();
        });
    }
}
