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
        Schema::table('conteneurs', function (Blueprint $table) {
            $table->unsignedBigInteger('iddepart')->after('idtypenvoi');
            $table->foreign('iddepart')
                ->references('id')
                ->on('adresses')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('iddestination')->after('iddepart');
            $table->foreign('iddestination')
                ->references('id')
                ->on('adresses')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
