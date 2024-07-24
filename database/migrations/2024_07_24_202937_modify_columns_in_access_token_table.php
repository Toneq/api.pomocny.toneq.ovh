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
        Schema::table('access_token', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable()->after('user')->change();
            $table->timestamp('updated_at')->nullable()->after('created_at')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('access_token', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable()->after('type')->change();
            $table->timestamp('updated_at')->nullable()->after('created_at')->change();
        });
    }
};
