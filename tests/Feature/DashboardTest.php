<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_sees_the_management_dashboard(): void
    {
        $user = $this->createUserWithRole('ADMIN', 'Administrateur');
        $this->createDashboardTransactions();

        $this->actingAs($user)
            ->withSession(['config' => 'back'])
            ->get('/')
            ->assertOk()
            ->assertViewIs('back.accueilA')
            ->assertSee('Expéditions récentes')
            ->assertSee('Transferts récents')
            ->assertSee('Approvisionnements de caisse')
            ->assertViewHas('dashboardStats', fn (array $stats) => $stats['users'] === 1
                && $stats['pendingShipments'] === 1
                && $stats['pendingTransfers'] === 1)
            ->assertViewHas('cashboxPaymentComparison', fn ($comparison) => (float) $comparison->first()->expeditions_payees === 25.0
                && (float) $comparison->first()->transferts_payes === 20.0)
            ->assertViewHas('recentTransfers', fn ($transfers) => $transfers->first()->code === 'TRANS-TEST')
            ->assertViewHas('recentCashAdvances', fn ($advances) => $advances->count() === 1
                && (float) $advances->first()->montant === 500.0);
    }

    public function test_other_profiles_only_see_shortcuts_from_their_permissions(): void
    {
        $user = $this->createUserWithRole('OPERATEUR', 'Opérateur');
        $dashboardData = $this->createDashboardTransactions();
        DB::table('users')->where('id', $user->id)->update(['idcaisse' => $dashboardData['cashboxId']]);
        $user->refresh();
        $this->createTransactionsForOtherAddress($dashboardData['addressId'], $dashboardData['cashboxId'], $dashboardData['actorId'], $dashboardData['elementId'], $dashboardData['transferMethodId']);

        $this->actingAs($user)
            ->withSession(['config' => 'back', 'menusUser' => [2, 4, 6]])
            ->get('/')
            ->assertOk()
            ->assertViewIs('back.accueilB')
            ->assertSee('Expéditions')
            ->assertDontSee('Approvisionnement caisse')
            ->assertDontSee('Approvisionnements de caisse')
            ->assertViewHas('dashboardActions', fn (array $actions) => count($actions) === 2
                && collect($actions)->every(fn (array $action) => $action['permission'] !== 2))
            ->assertViewHas('recentExpeditions', fn ($expeditions) => $expeditions->count() === 1
                && $expeditions->first()->code === 'COLIS-TEST')
            ->assertViewHas('recentTransfers', fn ($transfers) => $transfers->count() === 2
                && $transfers->contains('code', 'TRANS-TEST')
                && !$transfers->contains('code', 'TRANS-OUTSIDE'));
    }

    private function createUserWithRole(string $code, string $name): User
    {
        $roleId = DB::table('roles')->insertGetId([
            'code' => $code,
            'name' => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userId = DB::table('users')->insertGetId([
            'name' => 'Test Dashboard',
            'username' => strtolower($code) . '-test',
            'email' => strtolower($code) . '@example.test',
            'password' => Hash::make('password'),
            'actif' => true,
            'idrole' => $roleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::findOrFail($userId);
    }

    private function createDashboardTransactions(): array
    {
        $now = now();
        $addressId = DB::table('adresses')->insertGetId([
            'libelle' => 'Cotonou',
            'pays' => 'Bénin',
            'devise' => 'XOF',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $cashboxId = DB::table('caisses')->insertGetId([
            'libelle' => 'Caisse principale',
            'devise' => 'XOF',
            'montant' => 0,
            'idadresse' => $addressId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $actorId = DB::table('acteurs')->insertGetId([
            'nom' => 'Acteur de test',
            'tel' => '90000000',
            'whatsap' => '90000000',
            'marque' => 'Test',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $elementId = DB::table('elements')->insertGetId([
            'libelle' => 'En cours',
            'nomtable' => 'etat',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $transferMethodId = DB::table('moyentransferts')->insertGetId([
            'libelle' => 'Espèces',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $expeditionId = DB::table('expeditions')->insertGetId([
            'datenreg' => $now,
            'montant' => 100,
            'montantpaye' => 10,
            'poids' => 1,
            'nbrcolis' => 1,
            'code' => 'COLIS-TEST',
            'marque' => 'Test',
            'idexpediteur' => $actorId,
            'idrecepteur' => $actorId,
            'idtypcolis' => $elementId,
            'idtypenvoi' => $elementId,
            'idmoyentransport' => $elementId,
            'iddepart' => $addressId,
            'iddestination' => $addressId,
            'idetat' => $elementId,
            'idcaisse' => $cashboxId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('paymentcolis')->insert([
            'payment_date' => $now,
            'montant' => 15,
            'idcolis' => $expeditionId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $transferId = DB::table('transferts')->insertGetId([
            'code' => 'TRANS-TEST',
            'datetransfert' => $now,
            'montdepot' => 30,
            'montretrait' => 25,
            'montpaye' => 20,
            'description' => 'Test',
            'taux' => 1,
            'datepayement' => $now,
            'idexpediteur' => $actorId,
            'idrecepteur' => $actorId,
            'idmoyentransfert' => $transferMethodId,
            'iddepart' => $addressId,
            'iddestination' => $addressId,
            'idetat' => $elementId,
            'idcaisse' => $cashboxId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('transferts')->insert([
            'code' => 'TRANS-PENDING',
            'datetransfert' => $now->copy()->subDay(),
            'montdepot' => 30,
            'montretrait' => 25,
            'montpaye' => 0,
            'description' => 'Transfert en attente',
            'taux' => 1,
            'datepayement' => null,
            'idexpediteur' => $actorId,
            'idrecepteur' => $actorId,
            'idmoyentransfert' => $transferMethodId,
            'iddepart' => $addressId,
            'iddestination' => $addressId,
            'idetat' => $elementId,
            'idcaisse' => $cashboxId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('operationfinancieres')->insert([
            'sens' => 'Sortie',
            'montant' => 20,
            'idsrce' => $transferId,
            'libsrce' => 'transferts',
            'idcaisse' => $cashboxId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('approcaisses')->insert([
            'dateappro' => $now,
            'montant' => 500,
            'observation' => 'Approvisionnement de test',
            'idsource' => $elementId,
            'idcaisse' => $cashboxId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return compact('addressId', 'cashboxId', 'actorId', 'elementId', 'transferMethodId');
    }

    private function createTransactionsForOtherAddress(
        int $addressId,
        int $cashboxId,
        int $actorId,
        int $elementId,
        int $transferMethodId
    ): void {
        $now = now();
        $otherAddressId = DB::table('adresses')->insertGetId([
            'libelle' => 'Porto-Novo',
            'pays' => 'Bénin',
            'devise' => 'XOF',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('expeditions')->insert([
            'datenreg' => $now,
            'montant' => 50,
            'montantpaye' => 0,
            'poids' => 1,
            'nbrcolis' => 1,
            'code' => 'COLIS-OUTSIDE',
            'marque' => 'Test',
            'idexpediteur' => $actorId,
            'idrecepteur' => $actorId,
            'idtypcolis' => $elementId,
            'idtypenvoi' => $elementId,
            'idmoyentransport' => $elementId,
            'iddepart' => $addressId,
            'iddestination' => $otherAddressId,
            'idetat' => $elementId,
            'idcaisse' => $cashboxId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('transferts')->insert([
            'code' => 'TRANS-OUTSIDE',
            'datetransfert' => $now,
            'montdepot' => 30,
            'montretrait' => 25,
            'montpaye' => 0,
            'description' => 'Transfert hors destination',
            'taux' => 1,
            'datepayement' => null,
            'idexpediteur' => $actorId,
            'idrecepteur' => $actorId,
            'idmoyentransfert' => $transferMethodId,
            'iddepart' => $addressId,
            'iddestination' => $otherAddressId,
            'idetat' => $elementId,
            'idcaisse' => $cashboxId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
