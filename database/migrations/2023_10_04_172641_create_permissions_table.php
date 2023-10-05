<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->longText('access_modules')->nullable();
            $table->timestamps();
        });
        $typeArr = ['admin','bdm','recruiter','tl_recruiter','tl_bdm'];
        foreach ($typeArr as $type){
            \Illuminate\Support\Facades\DB::table('permissions')->insert([
                'type' => $type,
                'created_at' =>\Carbon\Carbon::now(),
                'updated_at' =>\Carbon\Carbon::now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
