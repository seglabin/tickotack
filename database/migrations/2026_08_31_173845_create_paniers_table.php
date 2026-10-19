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
        Schema::create('paniers', function (Blueprint $table) {
            $table->id();
            $table->integer('qte')->default(1);
            $table->decimal('pu', 10, 2);
            $table->unsignedBigInteger('idcmd');
            $table->foreign('idcmd')
                  ->references('id')
                  ->on('commandes')
                  ->onDelete('cascade')
                  ->onUpdate('restrict');
            $table->unsignedBigInteger('idarticle');
            $table->foreign('idarticle')
                  ->references('id')
                  ->on('articles')
                  ->onDelete('cascade')
                  ->onUpdate('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paniers');
    }
};
