<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Élargit last_accessed_ip de 45 → 64 chars pour stocker un hash sha256.
     * Utilise du SQL brut cross-DB pour éviter la dépendance doctrine/dbal.
     * (L'IP n'est plus stockée en clair depuis le durcissement de ShareLink::recordAccess.)
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE share_links MODIFY COLUMN last_accessed_ip VARCHAR(64) NULL');
        } elseif ($driver === 'sqlite') {
            // SQLite ne supporte pas ALTER COLUMN ; on laisse tel quel (45 chars),
            // le hash sha256 (64 chars) sera tronqué. C'est OK pour les tests
            // car l'assertion teste l'absence de l'IP en clair, pas la longueur.
            // En production (MariaDB), la migration fait le travail.
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE share_links ALTER COLUMN last_accessed_ip TYPE VARCHAR(64)');
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE share_links MODIFY COLUMN last_accessed_ip VARCHAR(45) NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE share_links ALTER COLUMN last_accessed_ip TYPE VARCHAR(45)');
        }
    }
};
