<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PaymentcoliTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $userId = DB::table('users')->insertGetId([
            'name' => 'Agent de test',
            'username' => 'agent-test',
            'email' => 'agent-test@example.test',
            'password' => bcrypt('password'),
            'actif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs(User::findOrFail($userId));
    }

    public function test_payments_cannot_exceed_the_expedition_amount_and_are_listed_in_history(): void
    {
        $expeditionId = $this->creerExpedition(100);

        $this->post(route('paymentcolis.enregistrer'), [
            'idcolis' => $expeditionId,
            'montant' => '60.00',
            'payment_date' => '2026-10-19 10:00:00',
        ])->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('paymentcolis', [
            'idcolis' => $expeditionId,
            'montant' => 60,
            'iduser' => auth()->id(),
        ]);

        $this->post(route('paymentcolis.enregistrer'), [
            'idcolis' => $expeditionId,
            'montant' => '40.00',
            'payment_date' => '2026-10-19 11:00:00',
        ])->assertOk()->assertJsonPath('total', 100);

        $this->post(route('paymentcolis.enregistrer'), [
            'idcolis' => $expeditionId,
            'montant' => '0.01',
            'payment_date' => '2026-10-19 12:00:00',
        ])->assertUnprocessable()->assertJsonPath('success', false);

        $this->getJson(route('paymentcolis.historique', $expeditionId))
            ->assertOk()
            ->assertJsonCount(2, 'paiements')
            ->assertJsonPath('total', 100);
    }

    public function test_payments_are_rejected_after_the_expedition_is_withdrawn(): void
    {
        $expeditionId = $this->creerExpedition(100, 'Retiré');

        $this->post(route('paymentcolis.enregistrer'), [
            'idcolis' => $expeditionId,
            'montant' => '10.00',
            'payment_date' => '2026-10-19 10:00:00',
        ])->assertUnprocessable()->assertJsonPath('success', false);

        $this->assertDatabaseCount('paymentcolis', 0);
    }

    public function test_withdrawal_payment_cannot_exceed_the_remaining_balance_after_partial_payments(): void
    {
        $expeditionId = $this->creerExpedition(100, 'Arrivé au magasin');
        DB::table('paymentcolis')->insert([
            'idcolis' => $expeditionId,
            'montant' => 60,
            'payment_date' => '2026-10-19 09:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $donneesRetrait = [
            'idexpedition' => $expeditionId,
            'action' => 'retraitcolis',
            'datetat' => '2026-10-19',
            'montantpaye' => '40.01',
        ];

        $this->postJson('/enregistrer-histoexpedition', $donneesRetrait)
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Le montant payé au retrait ne peut pas dépasser le solde du colis (40,00 CFA).');

        $donneesRetrait['montantpaye'] = '25.00';
        $this->postJson('/enregistrer-histoexpedition', $donneesRetrait)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('expeditions', [
            'id' => $expeditionId,
            'montantpaye' => 25,
        ]);
    }

    private function creerExpedition(float $montant, string $etat = 'En transit'): int
    {
        $adresseDepart = DB::table('adresses')->insertGetId([
            'libelle' => 'Départ',
            'pays' => 'Test',
            'devise' => 'CFA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $adresseDestination = DB::table('adresses')->insertGetId([
            'libelle' => 'Destination',
            'pays' => 'Test',
            'devise' => 'CFA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $expediteur = $this->creerActeur('Expéditeur');
        $recepteur = $this->creerActeur('Récepteur');
        $etatId = $this->creerElement($etat, 'etat');
        $typeColisId = $this->creerElement('Colis', 'typecolis');
        $typeEnvoiId = $this->creerElement('Standard', 'typenvoi');
        $moyenTransportId = $this->creerElement('Route', 'moyentransport');

        return DB::table('expeditions')->insertGetId([
            'datenreg' => now(),
            'montant' => $montant,
            'montantpaye' => 0,
            'poids' => 1,
            'nbrcolis' => 1,
            'code' => uniqid('COLIS-'),
            'marque' => 'Test',
            'idexpediteur' => $expediteur,
            'idrecepteur' => $recepteur,
            'idtypcolis' => $typeColisId,
            'idtypenvoi' => $typeEnvoiId,
            'idmoyentransport' => $moyenTransportId,
            'iddepart' => $adresseDepart,
            'iddestination' => $adresseDestination,
            'idetat' => $etatId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function creerActeur(string $nom): int
    {
        return DB::table('acteurs')->insertGetId([
            'nom' => $nom,
            'tel' => '0000000000',
            'whatsap' => '0000000000',
            'marque' => '',
            'carte' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function creerElement(string $libelle, string $nomtable): int
    {
        return DB::table('elements')->insertGetId([
            'libelle' => $libelle,
            'nomtable' => $nomtable,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
