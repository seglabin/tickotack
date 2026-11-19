<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ConteneurNumeroTest extends TestCase
{
    use RefreshDatabase;

    public function test_suggested_number_uses_type_transport_month_and_sequence(): void
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
            'username' => 'conteneur-admin',
            'email' => 'conteneur-admin@example.test',
            'password' => Hash::make('password'),
            'actif' => true,
            'idrole' => $roleId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $typeId = DB::table('elements')->insertGetId([
            'libelle' => 'Normal',
            'nomtable' => 'typenvoi',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $transportId = DB::table('elements')->insertGetId([
            'libelle' => 'Bateau',
            'nomtable' => 'moyentransport',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $otherTransportId = DB::table('elements')->insertGetId([
            'libelle' => 'Avion',
            'nomtable' => 'moyentransport',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $consignataireId = DB::table('elements')->insertGetId([
            'libelle' => 'Consignataire test',
            'nomtable' => 'consignataire',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $typeColisId = DB::table('elements')->insertGetId([
            'libelle' => 'Colis test',
            'nomtable' => 'typecolis',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $expediteurId = DB::table('acteurs')->insertGetId([
            'nom' => 'Expéditeur test',
            'tel' => '0000000000',
            'whatsap' => '0000000000',
            'marque' => '',
            'carte' => '',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $recepteurId = DB::table('acteurs')->insertGetId([
            'nom' => 'Récepteur test',
            'tel' => '0000000000',
            'whatsap' => '0000000000',
            'marque' => '',
            'carte' => '',
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
        $destinationId = DB::table('adresses')->insertGetId([
            'libelle' => 'Porto-Novo',
            'pays' => 'Bénin',
            'devise' => 'XOF',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $user = User::findOrFail($userId);
        $dateDepart = $now->copy()->startOfMonth()->format('Y-m-d H:i:s');

        $this->actingAs($user)
            ->getJson(route('conteneur.numero-suggere', [
                'idtypenvoi' => $typeId,
                'idmoyentransport' => $transportId,
                'datedepart' => $dateDepart,
            ]))
            ->assertOk()
            ->assertJsonPath('numconteneur', 'NOR-' . $now->format('y/m') . 'B01');

        DB::flushQueryLog();
        DB::enableQueryLog();
        $response = $this->post('/enregistrer-conteneur', [
            'idtypenvoi' => $typeId,
            'idmoyentransport' => $transportId,
            'iddepart' => $addressId,
            'iddestination' => $destinationId,
            'datedepart' => $dateDepart,
            'typeconteneur' => 'STANDARD',
            'lienconteneur' => 'https://example.test/conteneur',
            'idconsignataire' => $consignataireId,
        ]);
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $response->assertRedirect(route('conteneur'));
        $this->assertFalse(collect($queries)->contains(
            static fn (array $query) => preg_match('/\bIN\s*\(\s*\)/i', $query['query']) === 1
        ));
        $firstId = DB::table('conteneurs')->value('id');
        $this->assertDatabaseHas('conteneurs', [
            'id' => $firstId,
            'numconteneur' => 'NOR-' . $now->format('y/m') . 'B01',
        ]);

        DB::table('conteneurs')->where('id', $firstId)->update([
            'numconteneur' => 'REFERENCE-CUSTOM-01',
        ]);
        $this->getJson(route('conteneur.numero-suggere', [
            'idtypenvoi' => $typeId,
            'idmoyentransport' => $transportId,
            'datedepart' => $dateDepart,
            'idenreg' => $firstId,
        ]))
            ->assertOk()
            ->assertJsonPath('numconteneur', 'REFERENCE-CUSTOM-01');

        $this->getJson(route('conteneur.numero-suggere', [
            'idtypenvoi' => $typeId,
            'idmoyentransport' => $transportId,
            'datedepart' => $dateDepart,
        ]))
            ->assertOk()
            ->assertJsonPath('numconteneur', 'NOR-' . $now->format('y/m') . 'B02');

        $this->getJson(route('conteneur.numero-suggere', [
            'idtypenvoi' => $typeId,
            'idmoyentransport' => $otherTransportId,
            'datedepart' => $dateDepart,
        ]))
            ->assertOk()
            ->assertJsonPath('numconteneur', 'NOR-' . $now->format('y/m') . 'A01');

        $this->getJson(route('conteneur.numero-suggere', [
            'idtypenvoi' => $typeId,
            'idmoyentransport' => $transportId,
            'datedepart' => $dateDepart,
            'idenreg' => $firstId,
        ]))
            ->assertOk()
            ->assertJsonPath('numconteneur', 'REFERENCE-CUSTOM-01');

        $etatEnregistrementId = DB::table('elements')
            ->where('nomtable', 'etat')
            ->whereRaw('LOWER(libelle) = ?', ['enregistrement'])
            ->value('id');
        $expeditionId = DB::table('expeditions')->insertGetId([
            'datenreg' => $now,
            'montant' => 100,
            'montantpaye' => 0,
            'montantcolis' => 100,
            'autrecharge' => 0,
            'poids' => 1,
            'nbrcolis' => 1,
            'code' => 'COLIS-CONTENEUR-TEST',
            'marque' => 'Test',
            'idexpediteur' => $expediteurId,
            'idrecepteur' => $recepteurId,
            'idtypcolis' => $typeColisId,
            'idtypenvoi' => $typeId,
            'idmoyentransport' => $transportId,
            'iddepart' => $addressId,
            'iddestination' => $destinationId,
            'idetat' => $etatEnregistrementId,
            'idconteneur' => $firstId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->post('/enregistrer-conteneur', [
            'idenreg' => $firstId,
            'idtypenvoi' => $typeId,
            'idmoyentransport' => $transportId,
            'iddepart' => $addressId,
            'iddestination' => $destinationId,
            'datedepart' => $dateDepart,
            'typeconteneur' => 'STANDARD',
            'lienconteneur' => 'https://example.test/conteneur',
            'idconsignataire' => $consignataireId,
        ])->assertRedirect(route('conteneur'));
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $this->assertFalse(collect($queries)->contains(
            static fn (array $query) => preg_match('/\bIN\s*\(\s*\)/i', $query['query']) === 1
        ));
        $this->assertDatabaseHas('expeditions', [
            'id' => $expeditionId,
            'idconteneur' => null,
            'idetat' => $etatEnregistrementId,
        ]);
    }
}
