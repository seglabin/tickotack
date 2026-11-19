<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
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
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('receipt_url', null);
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

    public function test_expedition_deletion_cascades_related_records_and_confirms_success(): void
    {
        $expeditionId = $this->creerExpedition(100);
        $etatId = DB::table('expeditions')->where('id', $expeditionId)->value('idetat');
        $caisseId = DB::table('caisses')->insertGetId([
            'libelle' => 'Caisse test',
            'devise' => 'CFA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('paymentcolis')->insert([
            'idcolis' => $expeditionId,
            'montant' => 25,
            'payment_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('histoexpeditions')->insert([
            'datetat' => now()->toDateString(),
            'idexpedition' => $expeditionId,
            'idetat' => $etatId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('operationfinancieres')->insert([
            'sens' => 'Entree',
            'montant' => 25,
            'idsrce' => $expeditionId,
            'libsrce' => 'expeditions',
            'idcaisse' => $caisseId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->get('/supprimer-expedition/' . $expeditionId)
            ->assertRedirect(route('expedition'))
            ->assertSessionHas('success', 'L’expédition et ses données liées supprimées avec succès.');

        $this->assertDatabaseMissing('expeditions', ['id' => $expeditionId]);
        $this->assertDatabaseMissing('paymentcolis', ['idcolis' => $expeditionId]);
        $this->assertDatabaseMissing('histoexpeditions', ['idexpedition' => $expeditionId]);
        $this->assertDatabaseMissing('operationfinancieres', [
            'libsrce' => 'expeditions',
            'idsrce' => $expeditionId,
        ]);
    }

    public function test_integrity_constraint_failure_keeps_financial_records_and_shows_an_alert(): void
    {
        $expeditionId = $this->creerExpedition(100);
        $etatId = DB::table('expeditions')->where('id', $expeditionId)->value('idetat');
        $caisseId = DB::table('caisses')->insertGetId([
            'libelle' => 'Caisse test',
            'devise' => 'CFA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('paymentcolis')->insert([
            'idcolis' => $expeditionId,
            'montant' => 25,
            'payment_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('histoexpeditions')->insert([
            'datetat' => now()->toDateString(),
            'idexpedition' => $expeditionId,
            'idetat' => $etatId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('operationfinancieres')->insert([
            'sens' => 'Entree',
            'montant' => 25,
            'idsrce' => $expeditionId,
            'libsrce' => 'expeditions',
            'idcaisse' => $caisseId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Schema::create('expedition_delete_constraints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expedition_id')
                ->constrained('expeditions')
                ->restrictOnDelete();
        });
        DB::table('expedition_delete_constraints')->insert(['expedition_id' => $expeditionId]);

        $this->get('/supprimer-expedition/' . $expeditionId)
            ->assertRedirect(route('expedition'))
            ->assertSessionHas(
                'error',
                'Suppression impossible : cette expédition est encore référencée par des données liées. Vérifiez ses paiements, son historique et ses opérations financières.'
            );

        $this->assertDatabaseHas('expeditions', ['id' => $expeditionId]);
        $this->assertDatabaseHas('paymentcolis', ['idcolis' => $expeditionId]);
        $this->assertDatabaseHas('histoexpeditions', ['idexpedition' => $expeditionId]);
        $this->assertDatabaseHas('operationfinancieres', [
            'libsrce' => 'expeditions',
            'idsrce' => $expeditionId,
        ]);
        $this->assertDatabaseHas('expedition_delete_constraints', ['expedition_id' => $expeditionId]);
        Schema::drop('expedition_delete_constraints');
    }

    public function test_expedition_delete_action_uses_the_named_server_route(): void
    {
        $expeditionId = $this->creerExpedition(100);
        session(['config' => 'expedition']);

        ob_start();
        tableau_html(
            [(object) ['id' => $expeditionId, 'code' => 'COLIS-TEST', 'idetat' => 1, 'etat' => 'En transit']],
            'Code',
            'code',
            'supprimer'
        );
        $html = ob_get_clean();

        $this->assertStringContainsString(
            'href="' . route('expedition.supprimer', ['id' => $expeditionId]) . '"',
            $html
        );
        $this->assertStringContainsString(
            'onclick="return confirm(\'Voulez-vous vraiment supprimer cette expédition ?\');"',
            $html
        );
    }

    public function test_other_delete_actions_keep_the_application_subdirectory_in_their_urls(): void
    {
        URL::forceRootUrl('http://localhost/tickotack/public');

        try {
            foreach (['conteneur', 'approcaisse', 'depense', 'transfert'] as $config) {
                session(['config' => $config]);
                ob_start();
                tableau_html(
                    [(object) ['id' => 123, 'code' => 'TEST']],
                    'Code',
                    'code',
                    'supprimer'
                );
                $html = ob_get_clean();

                $this->assertStringContainsString(
                    'href="http://localhost/tickotack/public/supprimer-' . $config . '/123"',
                    $html
                );
                $this->assertStringNotContainsString('onclick="supprimer(', $html);
            }
        } finally {
            URL::forceRootUrl(null);
        }
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

    public function test_full_payment_records_the_withdrawal_and_returns_a_printable_receipt(): void
    {
        $expeditionId = $this->creerExpedition(100, 'Arrivé au magasin');
        DB::table('paymentcolis')->insert([
            'idcolis' => $expeditionId,
            'montant' => 60,
            'payment_date' => '2026-10-19 09:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->postJson(route('paymentcolis.enregistrer'), [
            'idcolis' => $expeditionId,
            'montant' => '40.00',
            'payment_date' => '2026-10-19 11:00:00',
        ]);

        $receiptUrl = route('expedition.recu-retrait', [
            'id' => $expeditionId,
            'imprimer' => 1,
        ]);
        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Paiement du colis enregistré et retrait effectué.')
            ->assertJsonPath('total', 100)
            ->assertJsonPath('receipt_url', $receiptUrl);

        $etatRetireId = DB::table('elements')
            ->where('nomtable', 'etat')
            ->where('libelle', 'Retiré')
            ->value('id');
        $this->assertDatabaseHas('expeditions', [
            'id' => $expeditionId,
            'idetat' => $etatRetireId,
            'montantpaye' => 0,
        ]);
        $this->assertDatabaseHas('histoexpeditions', [
            'idexpedition' => $expeditionId,
            'idetat' => $etatRetireId,
            'datetat' => '2026-10-19',
        ]);

        DB::table('acteurs')
            ->where('id', DB::table('expeditions')->where('id', $expeditionId)->value('idrecepteur'))
            ->update(['carte' => 'CARTE_ACT_6ac3a9371585f6ac3a93715863.jpg']);

        $this->get($receiptUrl)
            ->assertOk()
            ->assertSee('window.print()', false)
            ->assertSee('data:image/png;base64,', false)
            ->assertSee('data:image/jpeg;base64,', false)
            ->assertSee('Montant payé : 100 CFA');

        $this->get(route('expedition.recu-retrait', $expeditionId))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_transfer_receipt_embeds_logo_and_receiver_card_in_the_pdf(): void
    {
        $transfert = (object) [
            'iddepart' => 1,
            'iddestination' => 2,
            'code' => 'TRANSFERT-TEST',
            'montpaye' => 100,
            'montretrait' => 100,
            'montdepot' => 100,
            'datepayement' => '2026-10-19 11:00:00',
            'expediteur' => 'Expéditeur test',
            'recepteur' => 'Récepteur test',
            'carte_recepteur' => 'CARTE_ACT_6ac3a9371585f6ac3a93715863.jpg',
            'cartexpediteur' => null,
            'villedepart' => 'Départ test',
        ];
        $donnees = ['transfert' => $transfert, 'module' => 'recuretrait'];
        $html = view('back.transfert-recu', $donnees)->render();

        $this->assertStringContainsString('data:image/png;base64,', $html);
        $this->assertStringContainsString('data:image/jpeg;base64,', $html);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('back.transfert-recu', $donnees)
            ->setPaper('A4', 'landscape')
            ->output();
        $this->assertStringStartsWith('%PDF-', $pdf);
    }

    public function test_full_payment_before_arrival_does_not_register_withdrawal_or_return_receipt(): void
    {
        $expeditionId = $this->creerExpedition(100, 'En transit');

        $this->postJson(route('paymentcolis.enregistrer'), [
            'idcolis' => $expeditionId,
            'montant' => '100.00',
            'payment_date' => '2026-10-19 11:00:00',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Paiement du colis enregistré.')
            ->assertJsonPath('total', 100)
            ->assertJsonPath('receipt_url', null);

        $this->assertDatabaseHas('expeditions', [
            'id' => $expeditionId,
            'idetat' => DB::table('elements')
                ->where('nomtable', 'etat')
                ->where('libelle', 'En transit')
                ->value('id'),
        ]);
        $this->assertDatabaseMissing('histoexpeditions', [
            'idexpedition' => $expeditionId,
            'idetat' => DB::table('elements')
                ->where('nomtable', 'etat')
                ->where('libelle', 'Retiré')
                ->value('id'),
        ]);
    }

    public function test_withdrawal_receipt_is_listed_in_the_expedition_actions_menu(): void
    {
        $expeditionId = $this->creerExpedition(100, 'Retiré');
        session(['config' => 'expedition']);
        $ligneExpedition = (object) [
            'id' => $expeditionId,
            'code' => 'COLIS-TEST',
            'etat' => 'Retiré',
            'idetat' => DB::table('elements')
                ->where('nomtable', 'etat')
                ->where('libelle', 'Retiré')
                ->value('id'),
        ];

        ob_start();
        tableau_html([$ligneExpedition], 'Code', 'code', 'recu');
        $html = ob_get_clean();

        $this->assertStringContainsString('expedition-actions-menu', $html);
        $this->assertStringContainsString(
            '<a href="' . route('expedition.recu-retrait', $expeditionId) . '" target="_blank" rel="noopener" class="dropdown-item">',
            $html
        );
        $this->assertStringNotContainsString('title="Imprimer le reçu de retrait"', $html);
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

    public function test_fully_paid_expedition_can_be_withdrawn_without_an_additional_payment(): void
    {
        $expeditionId = $this->creerExpedition(100, 'Arrivé au magasin');
        DB::table('paymentcolis')->insert([
            'idcolis' => $expeditionId,
            'montant' => 100,
            'payment_date' => '2026-10-19 09:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->postJson('/enregistrer-histoexpedition', [
            'idexpedition' => $expeditionId,
            'action' => 'retraitcolis',
            'datetat' => '2026-10-19',
            'montantpaye' => '0.00',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Retrait effectué avec succès.')
            ->assertJsonPath('receipt_url', route('expedition.recu-retrait', [
                'id' => $expeditionId,
                'imprimer' => 1,
            ]));

        $this->get(route('expedition.recu-retrait', [
            'id' => $expeditionId,
            'imprimer' => 1,
        ]))
            ->assertOk()
            ->assertSee('window.print()', false)
            ->assertSee('REÇU DE RETRAIT DE COLIS');

        $etatRetireId = DB::table('elements')
            ->where('nomtable', 'etat')
            ->where('libelle', 'Retiré')
            ->value('id');
        $this->assertDatabaseHas('expeditions', [
            'id' => $expeditionId,
            'idetat' => $etatRetireId,
            'montantpaye' => 0,
        ]);
        $this->assertDatabaseHas('histoexpeditions', [
            'idexpedition' => $expeditionId,
            'idetat' => $etatRetireId,
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
            'montantcolis' => $montant,
            'autrecharge' => 0,
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
