<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stuntings', function (Blueprint $table) {
            $table->id();
            $table->string('desa', 100);
            $table->unsignedInteger('kasus')->default(0);
            $table->unsignedInteger('populasi')->default(0);
            // Simpan periode sebagai tanggal (pakai hari pertama bulan)
            $table->date('period'); 
            $table->timestamps();

            $table->index(['desa', 'period']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('stuntings');
    }
};
