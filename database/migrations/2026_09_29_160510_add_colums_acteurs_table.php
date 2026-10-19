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
         Schema::table('acteurs', function (Blueprint $table) {  
             $table->unsignedBigInteger('iduser')->nullable()->after('carte');
            $table->foreign('iduser')
                ->references('id')
                ->on('users')
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
