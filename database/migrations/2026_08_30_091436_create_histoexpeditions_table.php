<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. datetat, reference,idexpedition,idetat
     */
    public function up(): void
    {
        Schema::create('histoexpeditions', function (Blueprint $table) {
            $table->id();
            $table->date('datetat');
            $table->string('reference')->nullable();
            $table->unsignedBigInteger('idexpedition');
            $table->foreign('idexpedition')
                ->references('id')
                ->on('expeditions')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('idetat');
            $table->foreign('idetat')
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
        Schema::dropIfExists('histoexpeditions');
    }
};
