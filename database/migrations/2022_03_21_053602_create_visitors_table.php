<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_name');
            $table->string('visitor_email');
            $table->string('visitor_mobile_no');
            $table->string('visitor_meet_person_name');
            $table->string('visitor_image')->nullable();
            $table->string('visitor_department');
            $table->string('visitor_reason_to_meet');
            $table->dateTime('visitor_enter_time')->nullable();
            $table->dateTime('visitor_out_time')->nullable();
            $table->string('unique_token')->unique();
            $table->enum('visitor_status', ['In', 'Out'])->nullable()->default(null);
            $table->integer('visitor_enter_by')->nullable();
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
        Schema::dropIfExists('visitors');
    }
}
