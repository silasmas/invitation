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
        Schema::table('short_links', function (Blueprint $table) {
                $table->unsignedBigInteger('ceremonie_id')->after('reference')->nullable();

        // Index combiné pour éviter les doublons par cérémonie
        $table->unique(['ceremonie_id', 'reference']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('short_links', function (Blueprint $table) {
             $table->dropUnique(['ceremonie_id', 'reference']);
        $table->dropColumn('ceremonie_id');
        });
    }
};
