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
        Schema::create('expeditions', function (Blueprint $table) {
            $table->id();
            $table->datetime('datenreg');
            $table->double('montant')->default(0);
            $table->double('montantpaye')->default(0);
            $table->double('poids')->default(0);
            $table->integer('nbrcolis')->default(1);
            $table->string('code')->unique();
            $table->string('marque');
            $table->unsignedBigInteger('iddemarcheur')->nullable();
            $table->foreign('iddemarcheur')
                ->references('id')
                ->on('acteurs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('idexpediteur');
            $table->foreign('idexpediteur')
                ->references('id')
                ->on('acteurs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('idrecepteur');
            $table->foreign('idrecepteur')
                ->references('id')
                ->on('acteurs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
            $table->unsignedBigInteger('idetat');
            $table->foreign('idetat')
                ->references('id')
                ->on('elements')
                ->onDelete('cascade')
                ->onUpdate('cascade');
             $table->unsignedBigInteger('idcaisse')->nullable();
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
        Schema::dropIfExists('expeditions');
    }
};

