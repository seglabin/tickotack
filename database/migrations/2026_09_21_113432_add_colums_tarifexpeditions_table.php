<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.  iddepart, iddestination
     */
    public function up(): void
    {
         Schema::table('tarifexpeditions', function (Blueprint $table) {           
            $table->unsignedBigInteger('iddepart')->nullable()->after('idtypcolis');
            $table->foreign('iddepart')
                ->references('id')
                ->on('adresses')
                ->onDelete('set null')
                ->onUpdate('cascade');            
            $table->unsignedBigInteger('iddestination')->nullable()->after('iddepart');
            $table->foreign('iddestination')
                ->references('id')
                ->on('adresses')
                ->onDelete('set null')
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
