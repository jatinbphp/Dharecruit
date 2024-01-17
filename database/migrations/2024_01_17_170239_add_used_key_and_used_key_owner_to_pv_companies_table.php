<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsedKeyAndUsedKeyOwnerToPvCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('p_v_companies', function (Blueprint $table) {
            $table->string('used_key')->nullable()->after('assigned_user_id');
            $table->integer('used_key_owner')->nullable()->after('used_key');
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
            $table->dropColumn('used_key');
            $table->dropColumn('used_key_owner');
        });
    }
}
