<?php

use App\Models\Groupe;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['pendding','send', 'accept', 'refuse'])->default('pendding');
            $table->boolean('confirmation')->nullable();
            $table->string('reference')->unique();
            $table->string('lien')->nullable();
            $table->json('boissons')->nullable();
            $table->text('cadeau')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Guest::class)->constrained()->onDelete('cascade');
        });
        Schema::table('invitations', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Ceremonie::class)->constrained()->onDelete('cascade');

        });
        Schema::table('invitations', function (Blueprint $table) {
            $table->foreignIdFor(Groupe::class)->constrained()->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
