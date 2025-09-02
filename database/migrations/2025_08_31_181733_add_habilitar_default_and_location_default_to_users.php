<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'habilitar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('habilitar')->default(true)->after('location');
            });
        } else {
            DB::statement("ALTER TABLE users MODIFY habilitar TINYINT(1) NOT NULL DEFAULT 1");
        }
    }
    public function down(): void {}
};
