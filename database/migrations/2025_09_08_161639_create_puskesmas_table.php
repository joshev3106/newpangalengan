<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('puskesmas', function (Blueprint $t) {
            $t->id();
            $t->string('nama')->unique();
            $t->string('tipe')->nullable(); // induk / pembantu / posyandu (opsional)
            $t->decimal('lat', 10, 7)->nullable();
            $t->decimal('lng', 10, 7)->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('puskesmas');
    }
};
