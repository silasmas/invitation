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
		Schema::create('events', function (Blueprint $table) {
        	$table->id();
        	$table->string('nom')->nullable();
			$table->date('date');
			$table->string('lieu');
			$table->text('description')->nullable();
            $table->enum('status', ['brouillon', 'actif', 'termine'])->default('brouillon');
        	$table->timestamps();
        });

		Schema::table('events', function (Blueprint $table) {
                    $table->foreignIdFor(\App\Models\User::class)->constrained()->onDelete('cascade');

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
