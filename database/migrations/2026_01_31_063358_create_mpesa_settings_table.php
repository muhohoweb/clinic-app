<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mpesa_settings', function (Blueprint $table) {
            $table->id();
            $table->string('consumer_key')->nullable();
            $table->string('consumer_secret')->nullable();
            $table->string('shortcode')->nullable();
            $table->string('passkey')->nullable();
            $table->string('base_url')->default('https://api.safaricom.co.ke');
            $table->string('callback_url')->nullable();
            $table->string('environment')->default('sandbox'); // sandbox or production
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mpesa_settings');
    }
};