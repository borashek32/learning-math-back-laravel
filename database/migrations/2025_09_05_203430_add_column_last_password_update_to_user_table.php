<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_password_update_date')->nullable();
        });
        \Illuminate\Support\Facades\DB::statement('update users set last_password_update_date = created_at');

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_password_update_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_password_update_date');
        });
    }
};
