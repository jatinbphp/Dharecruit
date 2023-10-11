<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('requirement_id');
            $table->string('location')->nullable();
            $table->string('phone')->nullable();
            $table->string('employer_detail')->nullable();
            $table->string('work_authorization')->nullable();
            $table->string('recruiter_rate')->nullable();
            $table->string('last_4_ssn')->nullable();
            $table->string('education_details')->nullable();
            $table->string('resume_experience')->nullable();
            $table->string('linkedin_id')->nullable();
            $table->string('relocation')->nullable();
            $table->string('vendor_rate')->nullable();
            $table->text('notes')->nullable();
            $table->text('documents')->nullable();
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('submissions');
    }
}
