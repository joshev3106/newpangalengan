<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('stuntings', function (Blueprint $table) {
            $table->unique(['desa','period']);
        });
    }
    public function down(): void {
        Schema::table('stuntings', function (Blueprint $table) {
            $table->dropUnique(['stuntings_desa_period_unique']); // nama index default
        });
    }
};
