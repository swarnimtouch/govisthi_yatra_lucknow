<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('mobile', 15)->nullable();
            $table->string('email')->nullable();
            $table->string('city');
            $table->date('event_date');
            $table->string('photo_original')->nullable();
            $table->string('photo_cropped')->nullable();
            $table->string('generated_banner')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
