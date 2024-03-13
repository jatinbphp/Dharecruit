<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsShowRecruiterAfterUpdateAndIsUpdateRequirementFiledToRequirementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requirements', function (Blueprint $table) {
            $table->text('is_show_recruiter_after_update')->nullable()->after('is_show_recruiter');
            $table->integer('is_update_requirement')->default(0)->after('is_show_recruiter');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requirements', function (Blueprint $table) {
            $table->dropColumn('is_show_recruiter_after_update');
            $table->dropColumn('is_update_requirement');
        });
    }
}
