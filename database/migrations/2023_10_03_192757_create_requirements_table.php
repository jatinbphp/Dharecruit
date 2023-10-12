<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('requirements', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('job_id')->nullable();
            $table->string('job_title')->nullable();
            $table->string('no_of_position')->nullable();
            $table->string('experience')->nullable();
            $table->string('location')->nullable();
            $table->string('work_type')->nullable();
            $table->string('duration')->nullable();
            $table->string('visa')->nullable();
            $table->string('client')->nullable();
            $table->string('vendor_rate')->nullable();
            $table->string('my_rate')->nullable();
            $table->string('priority')->nullable();
            $table->text('reason')->nullable();
            $table->string('term')->nullable();
            $table->integer('category')->nullable();
            $table->integer('moi')->nullable();
            $table->string('job_keyword')->nullable();
            $table->string('notes')->nullable();
            $table->string('description')->nullable();
            $table->string('pv_company_name')->nullable();
            $table->string('poc_name')->nullable();
            $table->string('poc_email')->nullable();
            $table->string('poc_phone_number')->nullable();
            $table->string('poc_location')->nullable();
            $table->string('pv_company_location')->nullable();
            $table->string('client_name')->nullable();
            $table->string('display_client')->nullable();
            $table->text('recruiter')->nullable();
            $table->string('color')->nullable();
            $table->text('candidate')->nullable();
            $table->string('status')->default('active');
            $table->integer('submissionCounter')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requirements');
    }
}
