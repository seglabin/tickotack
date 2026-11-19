<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FinancialOperationDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_a_cash_advance_removes_its_income_and_recalculates_the_cashbox(): void
    {
        $userId = $this->authenticateAsAdmin();
        $caisseId = $this->createCashbox();
        $ancienneCaisseId = $this->createCashbox();
        $sourceId = DB::table('elements')->insertGetId([
            'libelle' => 'Source test',
            'nomtable' => 'sourceappro',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $approvisionnementId = DB::table('approcaisses')->insertGetId([
            'dateappro' => now(),
            'montant' => 50,
            'observation' => 'Approvisionnement test',
            'idsource' => $sourceId,
            'idcaisse' => $caisseId,
            'iduser' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->createOperation('Entree', 50, $approvisionnementId, 'approcaisses', $caisseId);
        $this->createOperation('Entree', 12, $approvisionnementId, 'approcaisses', $ancienneCaisseId);
        $this->createOperation('Entree', 30, 101, 'autre', $caisseId);
        $this->createOperation('Sortie', 5, 102, 'autre', $caisseId);
        $this->createOperation('Entree', 10, 103, 'autre', $ancienneCaisseId);
        $this->createOperation('Sortie', 2, 104, 'autre', $ancienneCaisseId);
        DB::table('caisses')->where('id', $caisseId)->update(['montant' => 75]);
        DB::table('caisses')->where('id', $ancienneCaisseId)->update(['montant' => 20]);

        $this->get('/supprimer-approcaisse/' . $approvisionnementId)
            ->assertRedirect(route('approcaisse'))
            ->assertSessionHas('success', 'Approvisionnement supprimé et solde de caisse régularisé.');

        $this->assertDatabaseMissing('approcaisses', ['id' => $approvisionnementId]);
        $this->assertDatabaseMissing('operationfinancieres', [
            'idsrce' => $approvisionnementId,
            'libsrce' => 'approcaisses',
        ]);
        $this->assertDatabaseHas('operationfinancieres', [
            'idsrce' => 101,
            'libsrce' => 'autre',
        ]);
        $this->assertDatabaseHas('caisses', ['id' => $caisseId, 'montant' => 25]);
        $this->assertDatabaseHas('caisses', ['id' => $ancienneCaisseId, 'montant' => 8]);
    }

    public function test_deleting_an_expense_removes_its_outflow_and_recalculates_the_cashbox(): void
    {
        $userId = $this->authenticateAsAdmin();
        $caisseId = $this->createCashbox();
        $ancienneCaisseId = $this->createCashbox();
        $depenseId = DB::table('depenses')->insertGetId([
            'datedepense' => now(),
            'libelle' => 'Dépense test',
            'montantdepense' => 50,
            'idcaisse' => $caisseId,
            'iduser' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->createOperation('Sortie', 50, $depenseId, 'depenses', $caisseId);
        $this->createOperation('Sortie', 15, $depenseId, 'depenses', $ancienneCaisseId);
        $this->createOperation('Entree', 30, 101, 'autre', $caisseId);
        $this->createOperation('Sortie', 5, 102, 'autre', $caisseId);
        $this->createOperation('Entree', 10, 103, 'autre', $ancienneCaisseId);
        $this->createOperation('Sortie', 2, 104, 'autre', $ancienneCaisseId);
        DB::table('caisses')->where('id', $caisseId)->update(['montant' => -25]);
        DB::table('caisses')->where('id', $ancienneCaisseId)->update(['montant' => -7]);

        $this->get('/supprimer-depense/' . $depenseId)
            ->assertRedirect(route('depense'))
            ->assertSessionHas('success', 'Dépense supprimée et solde de caisse régularisé.');

        $this->assertDatabaseMissing('depenses', ['id' => $depenseId]);
        $this->assertDatabaseMissing('operationfinancieres', [
            'idsrce' => $depenseId,
            'libsrce' => 'depenses',
        ]);
        $this->assertDatabaseHas('operationfinancieres', [
            'idsrce' => 101,
            'libsrce' => 'autre',
        ]);
        $this->assertDatabaseHas('caisses', ['id' => $caisseId, 'montant' => 25]);
        $this->assertDatabaseHas('caisses', ['id' => $ancienneCaisseId, 'montant' => 8]);
    }

    private function authenticateAsAdmin(): int
    {
        $roleId = DB::table('roles')->insertGetId([
            'code' => 'ADMIN',
            'name' => 'Administrateur',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $userId = DB::table('users')->insertGetId([
            'name' => 'Administrateur test',
            'username' => 'admin-finance-test',
            'email' => 'admin-finance-test@example.test',
            'password' => bcrypt('password'),
            'actif' => true,
            'idrole' => $roleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs(User::findOrFail($userId));

        return $userId;
    }

    private function createCashbox(): int
    {
        return DB::table('caisses')->insertGetId([
            'libelle' => 'Caisse test',
            'devise' => 'CFA',
            'montant' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createOperation(
        string $sens,
        float $montant,
        int $sourceId,
        string $source,
        int $caisseId
    ): void {
        DB::table('operationfinancieres')->insert([
            'sens' => $sens,
            'montant' => $montant,
            'idsrce' => $sourceId,
            'libsrce' => $source,
            'idcaisse' => $caisseId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
