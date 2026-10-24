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
        Schema::create('mvtcaisses', function (Blueprint $table) {
            $table->id();
            $table->datetime('datemvt');
            $table->double('montantsortie');
            $table->double('montantentree');
            $table->text('observation')->nullable();
            $table->unsignedBigInteger('idcaissesortie');
            $table->foreign('idcaissesortie')
                ->references('id')
                ->on('caisses')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('idcaissentree');
            $table->foreign('idcaissentree')
                ->references('id')
                ->on('caisses')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('iduser');
            $table->foreign('iduser')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('mvtcaisses');
    }
};
