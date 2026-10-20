<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->uuid('reference')->nullable()->unique();
            $table->string('nomclient')->nullable();
            $table->string('emailclient')->nullable();
            $table->string('telephoneclient', 20)->nullable();
            $table->string('adresse_livraison')->nullable();
            $table->string('ville_livraison')->nullable();
            $table->string('pays_livraison')->nullable();
            $table->string('methode_paiement')->default('paiement_a_la_livraison');
            $table->decimal('sous_total', 12, 2)->default(0);
            $table->decimal('frais_livraison', 12, 2)->default(0);
            $table->decimal('taxe', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('statut')->default('En attente');
        });

        Schema::table('paniers', function (Blueprint $table) {
            $table->string('libelle')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('paniers', function (Blueprint $table) {
            $table->dropColumn('libelle');
        });

        Schema::table('commandes', function (Blueprint $table) {
            $table->dropUnique(['reference']);
            $table->dropColumn([
                'reference',
                'nomclient',
                'emailclient',
                'telephoneclient',
                'adresse_livraison',
                'ville_livraison',
                'pays_livraison',
                'methode_paiement',
                'sous_total',
                'frais_livraison',
                'taxe',
                'total',
                'statut',
            ]);
        });
    }
};
