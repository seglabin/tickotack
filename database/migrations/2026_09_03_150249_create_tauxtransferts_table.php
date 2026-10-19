<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. taux,iddepart, iddestination
     */
    public function up(): void
    {
        Schema::create('tauxtransferts', function (Blueprint $table) {
            $table->id();
            $table->double('taux');
             $table->unsignedBigInteger('iddepart');
            $table->foreign('iddepart')
                ->references('id')
                ->on('adresses')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('iddestination');
            $table->foreign('iddestination')
                ->references('id')
                ->on('adresses')
                ->onDelete('cascade')
                ->onUpdate('cascade');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tauxtransferts');
    }
};
