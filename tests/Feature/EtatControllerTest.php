<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EtatControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_menu_shows_entries_allowed_by_existing_operation_permissions(): void
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Agent des colis',
            'username' => 'agent-menu-etats',
            'email' => 'agent-menu-etats@example.test',
            'password' => bcrypt('password'),
            'actif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs(User::findOrFail($userId));
        session(['menusUser' => [4]]);

        $html = view('back.includes.menu', ['nouv' => null, 'retour' => null])->render();

        $this->assertStringContainsString('États', $html);
        $this->assertStringContainsString(route('etats.colis'), $html);
        $this->assertStringContainsString(route('etats.paiements'), $html);
        $this->assertStringNotContainsString(route('etats.transferts'), $html);
    }

    public function test_reports_filter_print_and_respect_the_users_expedition_scope(): void
    {
        $now = now();
        $userId = DB::table('users')->insertGetId([
            'name' => 'Agent des rapports',
            'username' => 'agent-rapports',
            'email' => 'agent-rapports@example.test',
            'password' => bcrypt('password'),
            'actif' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $adresseAgent = $this->creerAdresse('Cotonou');
        $adresseDestination = $this->creerAdresse('Lomé');
        $caisseId = DB::table('caisses')->insertGetId([
            'libelle' => 'Caisse Cotonou',
            'devise' => 'CFA',
            'montant' => 0,
            'idadresse' => $adresseAgent,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('users')->where('id', $userId)->update(['idcaisse' => $caisseId]);
        $this->actingAs(User::findOrFail($userId));

        $typeEnvoiId = $this->creerElement('Standard', 'typenvoi');
        $moyenTransportId = $this->creerElement('Route', 'moyentransport');
        $conteneurId = DB::table('conteneurs')->insertGetId([
            'numconteneur' => 'CONT-ETAT-01',
            'idtypenvoi' => $typeEnvoiId,
            'idmoyentransport' => $moyenTransportId,
            'iddepart' => $adresseAgent,
            'iddestination' => $adresseDestination,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $etatId = $this->creerElement('En transit', 'etat');
        $expeditionId = $this->creerExpedition($adresseAgent, $adresseDestination, $etatId, $conteneurId, 'COLIS-ETAT-01');
        DB::table('expeditions')->where('id', $expeditionId)->update(['montantpaye' => 100]);
        $horsPeriodeId = $this->creerExpedition($adresseAgent, $adresseDestination, $etatId, $conteneurId, 'COLIS-ETAT-02', '2026-10-20 10:00:00');
        $this->creerExpedition($this->creerAdresse('Parakou'), $this->creerAdresse('Accra'), $etatId, null, 'COLIS-HORS-AGENCE');

        DB::table('paymentcolis')->insert([
            [
                'idcolis' => $expeditionId,
                'montant' => 300,
                'payment_date' => '2026-11-04 10:00:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'idcolis' => $expeditionId,
                'montant' => 200,
                'payment_date' => '2026-11-08 10:00:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $periode = ['date_debut' => '2026-11-01', 'date_fin' => '2026-11-05'];
        $this->get(route('etats.colis', $periode + ['conteneur' => $conteneurId, 'statut' => $etatId]))
            ->assertOk()
            ->assertSee('COLIS-ETAT-01')
            ->assertSee('En transit')
            ->assertSee('600')
            ->assertSee('400')
            ->assertDontSee('COLIS-ETAT-02')
            ->assertDontSee('COLIS-HORS-AGENCE')
            ->assertSee(route('etats.paiements', ['colis' => $expeditionId]), false)
            ->assertSee('window.print()', false)
            ->assertSee('transparent-header', false)
            ->assertSee('assets/images/entete.png', false)
            ->assertSee('size: A4 landscape', false);

        $this->get(route('etats.paiements', $periode + ['conteneur' => $conteneurId, 'colis' => $expeditionId]))
            ->assertOk()
            ->assertSee('COLIS-ETAT-01')
            ->assertSee('300')
            ->assertDontSee('08/11/2026')
            ->assertSee('size: A4 portrait', false);

        $this->get(route('etats.paiements', ['colis' => $horsPeriodeId]))
            ->assertOk()
            ->assertSee('Aucun résultat');
    }

    public function test_financial_reports_filter_by_period_and_list_the_requested_columns(): void
    {
        $now = now();
        $userId = DB::table('users')->insertGetId([
            'name' => 'Agent financier',
            'username' => 'agent-financier',
            'email' => 'agent-financier@example.test',
            'password' => bcrypt('password'),
            'actif' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $adresseId = $this->creerAdresse('Cotonou');
        $caisseId = DB::table('caisses')->insertGetId([
            'libelle' => 'Caisse test',
            'devise' => 'CFA',
            'montant' => 0,
            'idadresse' => $adresseId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->actingAs(User::findOrFail($userId));

        DB::table('depenses')->insert([
            [
                'datedepense' => '2026-11-04 09:00:00',
                'libelle' => 'Transport',
                'montantdepense' => 150,
                'idcaisse' => $caisseId,
                'iduser' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'datedepense' => '2026-10-04 09:00:00',
                'libelle' => 'Dépense hors période',
                'montantdepense' => 50,
                'idcaisse' => $caisseId,
                'iduser' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
        DB::table('recettes')->insert([
            [
                'daterecette' => '2026-11-04 11:00:00',
                'libelle' => 'Frais de dossier',
                'montantrecette' => 250,
                'idcaisse' => $caisseId,
                'iduser' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'daterecette' => '2026-10-04 11:00:00',
                'libelle' => 'Recette hors période',
                'montantrecette' => 75,
                'idcaisse' => $caisseId,
                'iduser' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $periode = ['date_debut' => '2026-11-01', 'date_fin' => '2026-11-05'];
        $this->get(route('etats.depenses', $periode))
            ->assertOk()
            ->assertSee('Transport')
            ->assertSee('Caisse test')
            ->assertSee('Montant')
            ->assertDontSee('Dépense hors période');
        $this->get(route('etats.recettes', $periode))
            ->assertOk()
            ->assertSee('Frais de dossier')
            ->assertSee('Caisse test')
            ->assertDontSee('Recette hors période')
            ->assertSee('window.print()', false);
        $this->get(route('etats.depenses', ['date_fin' => '2026-11-05']))->assertOk();
        $this->get(route('etats.transferts', $periode))
            ->assertOk()
            ->assertSee('Montant Dépôt')
            ->assertSee('Montant Retrait')
            ->assertSee('Actions');
    }

    private function creerAdresse(string $libelle): int
    {
        return DB::table('adresses')->insertGetId([
            'libelle' => $libelle,
            'pays' => 'Test',
            'devise' => 'CFA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function creerElement(string $libelle, string $table): int
    {
        return DB::table('elements')->insertGetId([
            'libelle' => $libelle,
            'nomtable' => $table,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function creerExpedition(
        int $depart,
        int $destination,
        int $etat,
        ?int $conteneur,
        string $code,
        string $date = '2026-11-03 10:00:00'
    ): int {
        $expediteur = $this->creerActeur('Expéditeur '.$code);
        $recepteur = $this->creerActeur('Récepteur '.$code);

        return DB::table('expeditions')->insertGetId([
            'datenreg' => $date,
            'montant' => 1000,
            'montantpaye' => 0,
            'poids' => 1,
            'nbrcolis' => 1,
            'code' => $code,
            'marque' => 'Test',
            'idexpediteur' => $expediteur,
            'idrecepteur' => $recepteur,
            'idtypcolis' => $this->creerElement('Colis', 'typecolis'),
            'idtypenvoi' => $this->creerElement('Standard', 'typenvoi'),
            'idmoyentransport' => $this->creerElement('Route', 'moyentransport'),
            'iddepart' => $depart,
            'iddestination' => $destination,
            'idetat' => $etat,
            'idconteneur' => $conteneur,
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
            'marque' => 'Test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
