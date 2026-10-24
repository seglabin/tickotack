<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MvtcaisseTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving_updating_and_deleting_a_cash_movement_keeps_financial_operations_and_balances_in_sync(): void
    {
        $adminRole = $this->creerRole('ADMIN', 'Administrateur');
        $userId = DB::table('users')->insertGetId([
            'name' => 'Agent de test',
            'username' => 'agent-mvt-test',
            'email' => 'agent-mvt-test@example.test',
            'password' => bcrypt('password'),
            'actif' => true,
            'idrole' => $adminRole,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->actingAs(User::findOrFail($userId));

        $caisseSortie = $this->creerCaisse('Caisse de sortie');
        $caisseEntree = $this->creerCaisse('Caisse d’entrée');
        $autreCaisse = $this->creerCaisse('Autre caisse');

        $this->post('/enregistrer-mvtcaisse', [
            'datemvt' => '2026-10-24T09:00',
            'montantsortie' => '1 250',
            'montantentree' => '950',
            'idcaissesortie' => $caisseSortie,
            'idcaissentree' => $caisseEntree,
            'observation' => 'Mouvement initial',
        ])->assertRedirect(route('mvtcaisse'));

        $mouvementId = DB::table('mvtcaisses')->value('id');
        $this->assertDatabaseHas('operationfinancieres', [
            'sens' => 'Sortie',
            'montant' => 1250,
            'idsrce' => $mouvementId,
            'libsrce' => 'mvtcaisses',
            'idcaisse' => $caisseSortie,
        ]);
        $this->assertDatabaseHas('operationfinancieres', [
            'sens' => 'Entree',
            'montant' => 950,
            'idsrce' => $mouvementId,
            'libsrce' => 'mvtcaisses',
            'idcaisse' => $caisseEntree,
        ]);
        $this->assertDatabaseHas('caisses', ['id' => $caisseSortie, 'montant' => -1250]);
        $this->assertDatabaseHas('caisses', ['id' => $caisseEntree, 'montant' => 950]);
        $this->get(route('mvtcaisse'))->assertOk();

        $this->get('/modifier-mvtcaisse/' . $mouvementId)->assertOk();

        $this->post('/enregistrer-mvtcaisse', [
            'idenreg' => $mouvementId,
            'datemvt' => '2026-10-24T10:00',
            'montantsortie' => '1 500',
            'montantentree' => '1 400',
            'idcaissesortie' => $caisseSortie,
            'idcaissentree' => $autreCaisse,
            'observation' => 'Mouvement modifié',
        ])->assertRedirect(route('mvtcaisse'));

        $this->assertDatabaseCount('operationfinancieres', 2);
        $this->assertDatabaseHas('operationfinancieres', [
            'sens' => 'Entree',
            'montant' => 1400,
            'idsrce' => $mouvementId,
            'libsrce' => 'mvtcaisses',
            'idcaisse' => $autreCaisse,
        ]);
        $this->assertDatabaseHas('caisses', ['id' => $caisseSortie, 'montant' => -1500]);
        $this->assertDatabaseHas('caisses', ['id' => $caisseEntree, 'montant' => 0]);
        $this->assertDatabaseHas('caisses', ['id' => $autreCaisse, 'montant' => 1400]);

        $this->from('/modifier-mvtcaisse/' . $mouvementId)
            ->post('/enregistrer-mvtcaisse', [
                'idenreg' => $mouvementId,
                'datemvt' => '2026-10-24T10:00',
                'montantsortie' => '1 500',
                'montantentree' => '1 400',
                'idcaissesortie' => $caisseSortie,
                'idcaissentree' => $caisseSortie,
            ])
            ->assertRedirect('/modifier-mvtcaisse/' . $mouvementId)
            ->assertSessionHasErrors('idcaissesortie');
        $this->assertDatabaseCount('operationfinancieres', 2);

        $this->get('/supprimer-mvtcaisse/' . $mouvementId)->assertRedirect(route('mvtcaisse'));

        $this->assertDatabaseCount('mvtcaisses', 0);
        $this->assertDatabaseCount('operationfinancieres', 0);
        $this->assertDatabaseHas('caisses', ['id' => $caisseSortie, 'montant' => 0]);
        $this->assertDatabaseHas('caisses', ['id' => $autreCaisse, 'montant' => 0]);
    }

    public function test_non_admin_uses_the_cashbox_attached_to_their_profile_for_cash_out(): void
    {
        $adminRole = $this->creerRole('ADMIN', 'Administrateur');
        $userRole = $this->creerRole('AGENT', 'Agent');
        $caisseRattachee = $this->creerCaisse('Caisse rattachée');
        $caisseChoisieParLeClient = $this->creerCaisse('Caisse non autorisée');
        $caisseEntree = $this->creerCaisse('Caisse d’entrée');
        $autreUserId = $this->creerUtilisateur('agent-mvt-autre', $userRole);
        $userId = DB::table('users')->insertGetId([
            'name' => 'Agent de test',
            'username' => 'agent-mvt-non-admin',
            'email' => 'agent-mvt-non-admin@example.test',
            'password' => bcrypt('password'),
            'actif' => true,
            'idrole' => $userRole,
            'idcaisse' => $caisseRattachee,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->actingAs(User::findOrFail($userId));

        $this->get('/ajout-mvtcaisse')
            ->assertOk()
            ->assertSee('Caisse rattachée')
            ->assertSee('name="idcaissesortie" value="' . $caisseRattachee . '"', false)
            ->assertDontSee('name="idcaissesortie" id="idcaissesortie"', false);

        $this->post('/enregistrer-mvtcaisse', [
            'datemvt' => '2026-10-24T09:00',
            'montantsortie' => '500',
            'montantentree' => '450',
            'idcaissesortie' => $caisseChoisieParLeClient,
            'idcaissentree' => $caisseEntree,
            'observation' => 'Transaction émise par ma caisse',
        ])->assertRedirect(route('mvtcaisse'));

        $mouvementId = DB::table('mvtcaisses')->value('id');
        $this->assertDatabaseHas('mvtcaisses', [
            'id' => $mouvementId,
            'idcaissesortie' => $caisseRattachee,
        ]);
        $this->assertDatabaseHas('operationfinancieres', [
            'sens' => 'Sortie',
            'idsrce' => $mouvementId,
            'libsrce' => 'mvtcaisses',
            'idcaisse' => $caisseRattachee,
        ]);
        $this->assertDatabaseMissing('operationfinancieres', [
            'sens' => 'Sortie',
            'idsrce' => $mouvementId,
            'libsrce' => 'mvtcaisses',
            'idcaisse' => $caisseChoisieParLeClient,
        ]);

        DB::table('mvtcaisses')->insert([
            [
                'datemvt' => '2026-10-24 08:00:00',
                'montantsortie' => 300,
                'montantentree' => 250,
                'observation' => 'Transaction reçue par ma caisse',
                'idcaissesortie' => $caisseChoisieParLeClient,
                'idcaissentree' => $caisseRattachee,
                'iduser' => $autreUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'datemvt' => '2026-10-24 07:00:00',
                'montantsortie' => 200,
                'montantentree' => 180,
                'observation' => 'Transaction sans ma caisse',
                'idcaissesortie' => $caisseChoisieParLeClient,
                'idcaissentree' => $caisseEntree,
                'iduser' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $liste = $this->get(route('mvtcaisse'))
            ->assertOk()
            ->assertSee('Transaction émise par ma caisse')
            ->assertSee('Transaction reçue par ma caisse')
            ->assertDontSee('Transaction sans ma caisse');

        $transactionRecueId = DB::table('mvtcaisses')
            ->where('observation', 'Transaction reçue par ma caisse')
            ->value('id');
        $liste->assertSee('modifier-mvtcaisse/' . $mouvementId)
            ->assertDontSee('modifier-mvtcaisse/' . $transactionRecueId)
            ->assertSee("supprimer('supprimer-mvtcaisse','" . $mouvementId . "')", false)
            ->assertDontSee("supprimer('supprimer-mvtcaisse','" . $transactionRecueId . "')", false);

        $this->get('/modifier-mvtcaisse/' . $transactionRecueId)->assertForbidden();
        $this->post('/enregistrer-mvtcaisse', [
            'idenreg' => $transactionRecueId,
            'datemvt' => '2026-10-24T08:00',
            'montantsortie' => '300',
            'montantentree' => '250',
            'idcaissesortie' => $caisseRattachee,
            'idcaissentree' => $caisseChoisieParLeClient,
        ])->assertForbidden();
        $this->get('/supprimer-mvtcaisse/' . $transactionRecueId)->assertForbidden();
        $this->assertDatabaseHas('mvtcaisses', [
            'id' => $transactionRecueId,
            'iduser' => $autreUserId,
            'observation' => 'Transaction reçue par ma caisse',
        ]);

        $this->actingAs(User::findOrFail($userId))
            ->get('/modifier-mvtcaisse/' . $mouvementId)
            ->assertOk()
            ->assertSee('Caisse rattachée');
    }

    private function creerCaisse(string $libelle): int
    {
        return DB::table('caisses')->insertGetId([
            'libelle' => $libelle,
            'devise' => 'CFA',
            'montant' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function creerRole(string $code, string $name): int
    {
        return DB::table('roles')->insertGetId([
            'code' => $code,
            'name' => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function creerUtilisateur(string $username, int $roleId): int
    {
        return DB::table('users')->insertGetId([
            'name' => $username,
            'username' => $username,
            'email' => $username . '@example.test',
            'password' => bcrypt('password'),
            'actif' => true,
            'idrole' => $roleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
