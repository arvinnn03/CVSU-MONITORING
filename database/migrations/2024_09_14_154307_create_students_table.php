<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('stud_id')->unique();
            $table->string('student_department');
            $table->string('student_course');
            $table->dateTime('student_enter_time')->nullable();
            $table->dateTime('student_out_time')->nullable();
            $table->enum('student_status', ['In', 'Out'])->default('Out');
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
        Schema::dropIfExists('students');
    }
}
