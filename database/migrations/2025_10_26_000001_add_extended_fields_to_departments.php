<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'address')) {
                $table->text('address')->nullable()->after('description');
            }
            if (!Schema::hasColumn('departments', 'phone')) {
                $table->string('phone', 30)->nullable()->after('address');
            }
            if (!Schema::hasColumn('departments', 'contact_email')) {
                $table->string('contact_email')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('departments', 'city')) {
                $table->string('city')->nullable()->after('contact_email');
            }
            if (!Schema::hasColumn('departments', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            if (!Schema::hasColumn('departments', 'postal_code')) {
                $table->string('postal_code', 50)->nullable()->after('state');
            }
            if (!Schema::hasColumn('departments', 'country')) {
                $table->string('country', 100)->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('departments', 'extra_info')) {
                $table->text('extra_info')->nullable()->after('country');
            }
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'extra_info')) {
                $table->dropColumn('extra_info');
            }
            if (Schema::hasColumn('departments', 'country')) {
                $table->dropColumn('country');
            }
            if (Schema::hasColumn('departments', 'postal_code')) {
                $table->dropColumn('postal_code');
            }
            if (Schema::hasColumn('departments', 'state')) {
                $table->dropColumn('state');
            }
            if (Schema::hasColumn('departments', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('departments', 'contact_email')) {
                $table->dropColumn('contact_email');
            }
            if (Schema::hasColumn('departments', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('departments', 'address')) {
                $table->dropColumn('address');
            }
        });
    }
};
