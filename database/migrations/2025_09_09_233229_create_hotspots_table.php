<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('hotspots', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->decimal('lat', 10, 7);   // -90..90
            $table->decimal('lng', 10, 7);   // -180..180
            $table->unsignedTinyInteger('confidence')->default(0); // 0/90/95/99
            $table->unsignedInteger('cases')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('hotspots');
    }
};
