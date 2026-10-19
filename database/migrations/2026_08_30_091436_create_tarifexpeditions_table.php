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
        Schema::create('tarifexpeditions', function (Blueprint $table) {
            $table->id();
            $table->double('poids')->default(1);
            $table->double('prix');
            $table->unsignedBigInteger('idtypcolis');
            $table->foreign('idtypcolis')
                ->references('id')
                ->on('elements')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('idtypenvoi');
            $table->foreign('idtypenvoi')
                ->references('id')
                ->on('elements')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('idmoyentransport');
            $table->foreign('idmoyentransport')
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
        Schema::dropIfExists('tarifexpeditions');
    }
};
