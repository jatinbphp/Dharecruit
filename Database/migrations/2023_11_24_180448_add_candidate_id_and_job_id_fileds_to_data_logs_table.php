<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCandidateIdAndJobIdFiledsToDataLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('data_logs', function (Blueprint $table) {
            $table->integer('user_id')->default(0)->nullable()->after('id');
            $table->integer('candidate_id')->default(0)->nullable()->after('section_id');
            $table->integer('job_id')->default(0)->nullable()->after('candidate_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data_logs', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('candidate_id');
            $table->dropColumn('job_id');
        });
    }
}
