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
        Schema::create('paymentcolis', function (Blueprint $table) {
            $table->id();
            $table->datetime('payment_date')->default(now());
            $table->double('montant');
            $table->unsignedBigInteger('idcolis');
            $table->foreign('idcolis')
                ->references('id')
                ->on('expeditions')
                ->onDelete('set null')
                ->onUpdate('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paymentcolis');
    }
};
