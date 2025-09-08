<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('desa_profiles', function (Blueprint $t) {
            $t->foreignId('puskesmas_id')->nullable()->after('desa')->constrained('puskesmas')->nullOnDelete();
            $t->index('puskesmas_id');
        });
    }
    public function down(): void {
        Schema::table('desa_profiles', function (Blueprint $t) {
            $t->dropConstrainedForeignId('puskesmas_id');
        });
    }
};
