<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFiledFillingConfidentAndIsProfilePendingFiledToAssignToRecruiters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assign_to_recruiters', function (Blueprint $table) {
            $table->integer('filling_confident')->default(1)->comment('1 => Filling Confident Will Try, 2 => Filling Confident Yes')->after('recruiter_id');
            $table->integer('is_profile_pending')->default(0)->after('filling_confident');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assign_to_recruiters', function (Blueprint $table) {
            $table->dropColumn('filling_confident');
            $table->dropColumn('is_profile_pending');
        });
    }
}
