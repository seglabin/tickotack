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
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('conteneurs', function (Blueprint $table) {
            $table->unsignedBigInteger('idetat')->nullable()->after('poidstotal');
            $table->foreign('idetat')
                ->references('id')
                ->on('elements')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }
};
