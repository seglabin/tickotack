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
        Schema::create('transferts', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->datetime('datetransfert');
            $table->double('montdepot');
            $table->double('montretrait');
            $table->text('description');
            $table->double('taux')->default(1);
            $table->text('observation')->nullable();
            $table->string('carterecepteur')->nullable();
            $table->datetime('datepayement')->nullable();
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
            $table->unsignedBigInteger('idmoyentransfert');
            $table->foreign('idmoyentransfert')
                ->references('id')
                ->on('moyentransferts')
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
        Schema::dropIfExists('transferts');
    }
};
