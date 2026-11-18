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
         Schema::table('expeditions', function (Blueprint $table) {
            $table->double('montantcolis', 15, 2)->after('montantpaye');            
            $table->double('autrecharge', 15, 2)->after('montantcolis');            
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
