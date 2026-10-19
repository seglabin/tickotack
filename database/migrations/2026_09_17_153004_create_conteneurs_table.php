<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. libelle, numconteneur, idconsignataire, typeconteneur, coutcfa, lienconteneur
     */
    public function up(): void
    {
        Schema::create('conteneurs', function (Blueprint $table) {
            $table->id();
            // $table->string('libelle')->nullable();
            $table->string('numconteneur')->unique();
            $table->string('typeconteneur',30)->nullable();
            $table->decimal('coutcfa', 15, 2)->nullable();
            $table->string('lienconteneur')->nullable();
            $table->unsignedBigInteger('idconsignataire')->nullable();
            $table->foreign('idconsignataire')
                ->references('id')
                ->on('elements')
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
        Schema::dropIfExists('conteneurs');
    }
};
