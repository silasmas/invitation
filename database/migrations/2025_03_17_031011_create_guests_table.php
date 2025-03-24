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
		Schema::create('guests', function (Blueprint $table) {
        	$table->id();
        	$table->string('nom');
			$table->string('email')->nullable();
			$table->string('phone')->nullable();
			$table->enum('relation', ['famille', 'ami', 'collegue','autre'])->default('autre');
        	$table->timestamps();
        });
        Schema::table('guests', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Event::class)->constrained()->onDelete('cascade');

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
