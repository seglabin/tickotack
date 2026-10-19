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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('top')->default(false);
            $table->boolean('visible')->default(false);
            $table->unsignedBigInteger('idparent')->nullable();
            $table->foreign('idparent')
                  ->references('id')
                  ->on('categories')
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
        Schema::dropIfExists('categories');
    }
};
