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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->datetime('datecmd');
            $table->text('observation')->nullable();
            $table->unsignedBigInteger('idclient');
            $table->foreign('idclient')
                  ->references('id')
                  ->on('clients')
                  ->onDelete('cascade')
                  ->onUpdate('restrict');
            $table->unsignedBigInteger('idtypelivraison');
            $table->foreign('idtypelivraison')
                  ->references('id')
                  ->on('elements')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');
            $table->unsignedBigInteger('idlivreur')->nullable();
            $table->foreign('idlivreur')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null')
                  ->onUpdate('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
