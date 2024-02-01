<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailSentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_sents', function (Blueprint $table) {
            $table->id();
            $table->longText('data');
            $table->string('type');
            $table->tinyInteger('status')->default(0)->comment('1 => Mail Sent, 0 => Mail Not Sent');
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
        Schema::dropIfExists('mail_sents');
    }
}
