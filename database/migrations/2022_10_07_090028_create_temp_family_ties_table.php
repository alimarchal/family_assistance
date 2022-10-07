<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_family_ties', function (Blueprint $table) {
            $table->id();
            $table->timestamp('expiry_date_time', 0)->nullable();
            $table->unsignedBigInteger('my_id')->nullable();
            $table->unsignedBigInteger('head_id')->nullable();
            $table->string('accepted')->nullable();
            $table->string('tie_code')->nullable();
            $table->string('untie_request')->nullable();
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
        Schema::dropIfExists('temp_family_ties');
    }
};
