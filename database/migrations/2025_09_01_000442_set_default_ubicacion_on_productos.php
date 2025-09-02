<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Ajusta el tipo si el tuyo no es VARCHAR(255)
        DB::statement("ALTER TABLE productos MODIFY ubicacion VARCHAR(255) NOT NULL DEFAULT 'Principal'");

        DB::table('productos')
            ->whereNull('ubicacion')
            ->orWhere('ubicacion', '')
            ->update(['ubicacion' => 'Principal']);
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE productos ALTER COLUMN ubicacion DROP DEFAULT");
    }
};
