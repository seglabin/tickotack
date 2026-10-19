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
        Schema::create('approcaisses', function (Blueprint $table) {
            $table->id();
            $table->datetime('dateappro');
            $table->double('montant');
            $table->text('observation')->nullable();
            $table->unsignedBigInteger('idsource');
            $table->foreign('idsource')
                ->references('id')
                ->on('elements')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('idcaisse');
            $table->foreign('idcaisse')
                ->references('id')
                ->on('caisses')
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
        Schema::dropIfExists('approcaisses');
    }
};
