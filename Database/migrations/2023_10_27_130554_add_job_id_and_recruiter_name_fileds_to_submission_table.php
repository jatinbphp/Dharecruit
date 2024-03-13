<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJobIdAndRecruiterNameFiledsToSubmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->string('recruiter_name')->nullable()->after('status');
            $table->bigInteger('job_id')->default('0')->after('recruiter_name');
            $table->bigInteger('submission_id')->default('0')->after('job_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropColumn('recruiter_name');
            $table->dropColumn('job_id');
            $table->dropColumn('submission_id');
        });
    }
}
