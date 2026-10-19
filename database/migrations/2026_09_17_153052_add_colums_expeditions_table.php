<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. long, larg, haut, idconteneur
     */
    public function up(): void
    {
        Schema::table('expeditions', function (Blueprint $table) {
            $table->decimal('long', 15, 2)->nullable();
            $table->decimal('larg', 15, 2)->nullable();
            $table->decimal('haut', 15, 2)->nullable();
            $table->unsignedBigInteger('idconteneur')->nullable();
            $table->foreign('idconteneur')
                ->references('id')
                ->on('conteneurs')
                ->onDelete('set null')
                ->onUpdate('cascade');            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
