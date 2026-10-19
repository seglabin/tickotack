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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->double('prix')->default(0);
            $table->double('old_price')->default(0);
            $table->string('reference')->unique();
            $table->text('description')->nullable();
            $table->string('photo1')->nullable();
            $table->string('photo2')->nullable();
            $table->string('photo3')->nullable();
            $table->string('photo4')->nullable();
            $table->string('photo5')->nullable();
            $table->boolean('lot')->default(false);
            $table->boolean('carousel')->default(false);
            $table->unsignedBigInteger('idparent')->nullable();
            $table->foreign('idparent')
                  ->references('id')
                  ->on('articles')
                  ->onDelete('set null')
                  ->onUpdate('restrict');
            $table->unsignedBigInteger('idcategorie');
            $table->foreign('idcategorie')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade')
                  ->onUpdate('restrict');
            $table->unsignedBigInteger('idmarque')->nullable();
            $table->foreign('idmarque')
                  ->references('id')
                  ->on('marques')
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
        Schema::dropIfExists('articles');
    }
};
