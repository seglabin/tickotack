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
        Schema::create('rolepermissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idrole');
            $table->foreign('idrole')
                ->references('id')
                ->on('roles')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->unsignedBigInteger('idpermission');
            $table->foreign('idpermission')
                ->references('id')
                ->on('permissions')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rolepermissions');
    }
};
