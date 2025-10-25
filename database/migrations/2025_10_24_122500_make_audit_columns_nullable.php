<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw statements to avoid requiring doctrine/dbal for column changes.
        // Make auditable_type and auditable_id nullable so module-level audit entries without a model are allowed.
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `audit_logs` MODIFY COLUMN `auditable_type` VARCHAR(255) NULL");
            DB::statement("ALTER TABLE `audit_logs` MODIFY COLUMN `auditable_id` BIGINT UNSIGNED NULL");
        } else {
            // Fallback: try using the schema builder (may require doctrine/dbal)
            Schema::table('audit_logs', function (Blueprint $table) {
                $table->string('auditable_type')->nullable()->change();
                $table->unsignedBigInteger('auditable_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `audit_logs` MODIFY COLUMN `auditable_type` VARCHAR(255) NOT NULL");
            DB::statement("ALTER TABLE `audit_logs` MODIFY COLUMN `auditable_id` BIGINT UNSIGNED NOT NULL");
        } else {
            Schema::table('audit_logs', function (Blueprint $table) {
                $table->string('auditable_type')->nullable(false)->change();
                $table->unsignedBigInteger('auditable_id')->nullable(false)->change();
            });
        }
    }
};
