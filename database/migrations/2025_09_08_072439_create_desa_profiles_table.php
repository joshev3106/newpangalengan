<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('desa_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('desa', 100)->unique();     // 1 profil per desa
            $table->string('puskesmas_id', 150)->nullable();
            $table->string('faskes_terdekat', 150)->nullable();
            $table->unsignedTinyInteger('cakupan')->nullable(); // 0..100 (%)
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('desa_profiles');
    }
};
