<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('guard_name'); // Ensure this field is required
            $table->string('guard_email')->unique(); // Ensure email is unique
            $table->enum('guard_status', ['On Duty', 'Off Duty']);
            $table->string('otp')->nullable();
            $table->timestamp('expires_at')->nullable(); // Add expiration time if applicable
            $table->timestamps();
            
            // Optional: Add an index to the email column for faster lookups
            $table->index('guard_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('otps');
    }
}
