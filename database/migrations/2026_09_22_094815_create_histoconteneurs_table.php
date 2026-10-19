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
        Schema::create('histoconteneurs', function (Blueprint $table) {
            $table->id();
             $table->datetime('datetat');
            $table->string('observation')->nullable();
            $table->unsignedBigInteger('ideconteneur');
            $table->foreign('ideconteneur')
                ->references('id')
                ->on('conteneurs')
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
        Schema::dropIfExists('histoconteneurs');
    }
};
