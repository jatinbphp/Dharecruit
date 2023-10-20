<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmpFiledsToSubmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('employer_name')->nullable();
            $table->string('employee_name')->nullable();
            $table->string('employee_email')->nullable();
            $table->string('employee_phone')->nullable();
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
            $table->dropColumn('employer_name');
            $table->dropColumn('employee_name');
            $table->dropColumn('employee_email');
            $table->dropColumn('employee_phone');
        });
    }
}
