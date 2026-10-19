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
        Schema::create('historiquecmds', function (Blueprint $table) {
            $table->id();
            $table->dateTime('dateheure');
            $table->text('observation')->nullable();
            $table->unsignedBigInteger('idcmd');
            $table->foreign('idcmd')
                  ->references('id')
                  ->on('commandes')
                  ->onDelete('cascade')
                  ->onUpdate('restrict');
            $table->unsignedBigInteger('idetat');
            $table->foreign('idetat')
                  ->references('id')
                  ->on('elements')
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
        Schema::dropIfExists('historiquecmds');
    }
};
