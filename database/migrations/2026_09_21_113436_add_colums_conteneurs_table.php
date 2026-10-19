<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.  idtypenvoi, idmoyentransport, datedepart, douane, poidstotal
     */
    public function up(): void
    {
        Schema::table('conteneurs', function (Blueprint $table) {
            $table->unsignedBigInteger('idtypenvoi')->after('idconsignataire');
            $table->foreign('idtypenvoi')
            ->references('id')
            ->on('elements')
            ->onDelete('cascade')
            ->onUpdate('cascade');
            $table->unsignedBigInteger('idmoyentransport')->after('idtypenvoi');
            $table->foreign('idmoyentransport')
            ->references('id')
            ->on('elements')
            ->onDelete('cascade')
            ->onUpdate('cascade');
            $table->datetime('datedepart')->nullable()->after('idmoyentransport');
            $table->double('douane')->nullable()->after('datedepart');
            $table->double('poidstotal')->nullable()->after('douane');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
