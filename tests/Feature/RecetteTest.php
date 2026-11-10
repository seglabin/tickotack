<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RecetteTest extends TestCase
{
    use RefreshDatabase;

    public function test_recette_form_list_and_cashbox_operation_are_connected(): void
    {
        $now = now();
        $roleId = DB::table('roles')->insertGetId([
            'code' => 'ADMIN',
            'name' => 'Administrateur',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $userId = DB::table('users')->insertGetId([
            'name' => 'Administrateur test',
            'username' => 'recette-admin',
            'email' => 'recette-admin@example.test',
            'password' => Hash::make('password'),
            'actif' => true,
            'idrole' => $roleId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
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
        $user = User::findOrFail($userId);

        $this->actingAs($user)
            ->get('/ajout-recette')
            ->assertOk()
            ->assertSee('Date de la recette')
            ->assertSee('Montant');

        $this->actingAs($user)
            ->post('/enregistrer-recette', [
                'daterecette' => $now->format('Y-m-d\TH:i'),
                'libelle' => 'Vente de marchandises',
                'montantrecette' => '1 250',
                'idcaisse' => $cashboxId,
            ])
            ->assertRedirect(route('recette'));

        $recetteId = DB::table('recettes')->value('id');
        $this->assertDatabaseHas('operationfinancieres', [
            'idsrce' => $recetteId,
            'libsrce' => 'recettes',
            'sens' => 'Entree',
            'montant' => 1250,
            'idcaisse' => $cashboxId,
        ]);
        $this->assertDatabaseHas('caisses', ['id' => $cashboxId, 'montant' => 1250]);
        $this->get('/recette')->assertOk()->assertSee('Vente de marchandises');

        $otherRoleId = DB::table('roles')->insertGetId([
            'code' => 'OPERATEUR',
            'name' => 'Opérateur',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $otherUserId = DB::table('users')->insertGetId([
            'name' => 'Opérateur test',
            'username' => 'recette-operateur',
            'email' => 'recette-operateur@example.test',
            'password' => Hash::make('password'),
            'actif' => true,
            'idrole' => $otherRoleId,
            'idcaisse' => $cashboxId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->actingAs(User::findOrFail($otherUserId))
            ->get('/recette')
            ->assertOk()
            ->assertDontSee('Vente de marchandises');
        $this->get('/modifier-recette/' . $recetteId)->assertForbidden();

        $this->actingAs($user);
        $this->get('/modifier-recette/' . $recetteId)->assertOk()->assertSee("Modification d'une recette");
        $this->post('/enregistrer-recette', [
            'idenreg' => $recetteId,
            'daterecette' => $now->format('Y-m-d\TH:i'),
            'libelle' => 'Vente mise à jour',
            'montantrecette' => '2 000',
            'idcaisse' => $cashboxId,
        ])->assertRedirect(route('recette'));
        $this->assertDatabaseHas('caisses', ['id' => $cashboxId, 'montant' => 2000]);

        $this->get('/supprimer-recette/' . $recetteId)->assertRedirect(route('recette'));
        $this->assertDatabaseMissing('recettes', ['id' => $recetteId]);
        $this->assertDatabaseMissing('operationfinancieres', ['idsrce' => $recetteId, 'libsrce' => 'recettes']);
        $this->assertDatabaseHas('caisses', ['id' => $cashboxId, 'montant' => 0]);
    }
}
