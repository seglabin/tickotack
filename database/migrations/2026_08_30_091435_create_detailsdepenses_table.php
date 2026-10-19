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
        Schema::create('detailsdepenses', function (Blueprint $table) {
            $table->id();
            $table->double('pu')->default(0);
            $table->integer('qte')->default(1);
            $table->text('observation')->nullable();
            $table->unsignedBigInteger('iddepense');
            $table->foreign('iddepense')
                ->references('id')
                ->on('depenses')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('idarticledep');
            $table->foreign('idarticledep')
                ->references('id')
                ->on('elements')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailsdepenses');
    }
};
