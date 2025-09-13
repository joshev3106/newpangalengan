<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('desa_profiles', function (Blueprint $table) {
            $table->unsignedInteger('served')->nullable()->after('cakupan'); // pasien dilayani
        });
    }

    public function down(): void
    {
        Schema::table('desa_profiles', function (Blueprint $table) {
            $table->dropColumn('served');
        });
    }
};
