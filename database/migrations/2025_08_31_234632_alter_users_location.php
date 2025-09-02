<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ajusta el tipo si no es VARCHAR(255)
        if (Schema::hasColumn('users', 'location')) {
            DB::statement("ALTER TABLE users MODIFY location VARCHAR(255) NOT NULL DEFAULT 'Principal'");
        } else {
            DB::statement("ALTER TABLE users ADD location VARCHAR(255) NOT NULL DEFAULT 'Principal' AFTER email");
        }

        DB::table('users')
            ->whereNull('location')
            ->orWhere('location', '')
            ->update(['location' => 'Principal']);
    }

    public function down(): void
    {
        // quitar default (MySQL 8+)
        DB::statement("ALTER TABLE users ALTER COLUMN location DROP DEFAULT");
        // si tu versión no soporta DROP DEFAULT, necesitarás un MODIFY sin DEFAULT:
        // DB::statement("ALTER TABLE users MODIFY location VARCHAR(255) NULL");
    }
};
