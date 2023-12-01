<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusUpdatedAtFiledsToSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dateTime('bdm_status_updated_at')->nullable()->after('is_show');
            $table->dateTime('pv_status_updated_at')->nullable()->after('bdm_status_updated_at');
            $table->dateTime('interview_status_updated_at')->nullable()->after('pv_status_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('bdm_status_updated_at');
            $table->dropColumn('pv_status_updated_at');
            $table->dropColumn('interview_status_updated_at');
        });
    }
}
