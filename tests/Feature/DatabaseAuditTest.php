<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Auth;
use App\Support\DatabaseAuditLogger;
use Tests\TestCase;

class DatabaseAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_read_queries_do_not_resolve_authentication_from_the_audit_listener(): void
    {
        Auth::shouldReceive('check')->never();

        DatabaseAuditLogger::capture(new QueryExecuted(
            'select * from "users" where "id" = ? limit 1',
            [1],
            0,
            DB::connection()
        ));

        $this->assertDatabaseCount('database_audit_logs', 0);
    }

    public function test_database_writes_are_logged_with_sensitive_values_redacted(): void
    {
        $user = $this->createUser('audit-agent');
        Route::get('/_test/database-audit-write', function () {
            $id = DB::table('users')->insertGetId([
                'name' => 'Utilisateur suivi',
                'username' => 'tracked-user',
                'email' => 'private@example.test',
                'password' => 'secret-password',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('users')->where('id', $id)->update([
                'name' => 'Utilisateur modifié',
                'email' => 'updated@example.test',
            ]);
            DB::table('users')->where('id', $id)->delete();

            return response()->json(['id' => $id]);
        });

        $id = $this->actingAs($user)
            ->get('/_test/database-audit-write')
            ->assertOk()
            ->json('id');

        $logs = DB::table('database_audit_logs')
            ->where('user_id', $user->id)
            ->where('table_name', 'users')
            ->orderBy('id')
            ->get();

        $this->assertCount(3, $logs);
        $this->assertSame(['INSERT', 'UPDATE', 'DELETE'], $logs->pluck('action')->all());
        $this->assertSame((string) $id, $logs[0]->record_id);
        $this->assertSame('[masqué]', json_decode($logs[0]->changes, true)['email']['new']);
        $this->assertSame('[masqué]', json_decode($logs[0]->changes, true)['password']['new']);
        $this->assertSame('Utilisateur suivi', json_decode($logs[0]->changes, true)['name']['new']);
        $this->assertSame((string) $id, $logs[1]->record_id);
        $this->assertSame('[masqué]', json_decode($logs[1]->changes, true)['email']['new']);
        $this->assertSame('Utilisateur modifié', json_decode($logs[1]->changes, true)['name']['new']);
        $this->assertSame((string) $id, $logs[2]->record_id);

        $logsBeforeRead = DB::table('database_audit_logs')->count();
        DB::table('users')->where('id', $user->id)->first();
        $this->assertSame($logsBeforeRead, DB::table('database_audit_logs')->count());
    }

    public function test_non_admin_cannot_open_another_users_expense_for_editing(): void
    {
        $owner = $this->createUser('expense-owner');
        $otherUser = $this->createUser('expense-other');
        $caisseId = DB::table('caisses')->insertGetId([
            'libelle' => 'Caisse test',
            'devise' => 'CFA',
            'montant' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $depenseId = DB::table('depenses')->insertGetId([
            'datedepense' => now(),
            'libelle' => 'Dépense test',
            'montantdepense' => 100,
            'idcaisse' => $caisseId,
            'iduser' => $owner->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($otherUser)
            ->get('/modifier-depense/' . $depenseId)
            ->assertForbidden();
        $this->post('/enregistrer-depense', ['idenreg' => $depenseId])->assertForbidden();
        $this->get('/supprimer-depense/' . $depenseId)->assertForbidden();
        $this->assertDatabaseHas('depenses', [
            'id' => $depenseId,
            'libelle' => 'Dépense test',
            'iduser' => $owner->id,
        ]);
        $this->get(route('admin.historique-actions'))->assertForbidden();
    }

    public function test_non_admin_cannot_edit_or_withdraw_another_users_transfer(): void
    {
        $owner = $this->createUser('transfer-owner');
        $otherUser = $this->createUser('transfer-other');
        $addressIds = [];
        foreach (['Départ', 'Destination'] as $label) {
            $addressIds[] = DB::table('adresses')->insertGetId([
                'libelle' => $label,
                'pays' => 'Test',
                'devise' => 'CFA',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $caisseId = DB::table('caisses')->insertGetId([
            'libelle' => 'Caisse départ',
            'devise' => 'CFA',
            'montant' => 0,
            'idadresse' => $addressIds[0],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $actorIds = [];
        foreach (['Expéditeur', 'Récepteur'] as $name) {
            $actorIds[] = DB::table('acteurs')->insertGetId([
                'nom' => $name,
                'tel' => '0000000000',
                'whatsap' => '0000000000',
                'marque' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $moyenId = DB::table('moyentransferts')->insertGetId([
            'libelle' => 'Espèces',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $etatId = DB::table('elements')->insertGetId([
            'libelle' => 'En cours',
            'nomtable' => 'etat',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $transfertId = DB::table('transferts')->insertGetId([
            'code' => 'TRF-OWNERSHIP-TEST',
            'datetransfert' => now(),
            'montdepot' => 100,
            'montretrait' => 100,
            'description' => 'Test de propriété',
            'taux' => 1,
            'idexpediteur' => $actorIds[0],
            'idrecepteur' => $actorIds[1],
            'idmoyentransfert' => $moyenId,
            'iddepart' => $addressIds[0],
            'iddestination' => $addressIds[1],
            'idetat' => $etatId,
            'idcaisse' => $caisseId,
            'iduser' => $owner->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($otherUser)
            ->get('/modifier-transfert/' . $transfertId)
            ->assertForbidden();
        $this->postJson(route('valideretraitransfert'), ['idtransfert' => $transfertId])
            ->assertForbidden();
        $this->assertDatabaseHas('transferts', [
            'id' => $transfertId,
            'iduser' => $owner->id,
            'datepayement' => null,
        ]);
    }

    public function test_administrator_can_view_the_formatted_action_history(): void
    {
        DB::table('roles')->insert([
            'id' => 1,
            'code' => 'ADMIN',
            'name' => 'Administrateur',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $admin = $this->createUser('audit-admin', 1);
        DB::table('database_audit_logs')->insert([
            'user_id' => $admin->id,
            'action' => 'UPDATE',
            'table_name' => 'transferts',
            'record_id' => '42',
            'changes' => json_encode(['montant' => ['new' => 125]]),
            'route' => 'transfert.enregistrer',
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);
        DB::table('database_audit_logs')->insert([
            'user_id' => $admin->id,
            'action' => 'INSERT',
            'table_name' => 'transferts',
            'record_id' => '3',
            'created_at' => now(),
        ]);
        DB::table('database_audit_logs')->insert([
            'user_id' => $admin->id,
            'action' => 'UPDATE',
            'table_name' => 'caisses',
            'record_id' => '7',
            'changes' => json_encode(['montant' => ['new' => 5000]]),
            'created_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.historique-actions'))
            ->assertOk()
            ->assertSee('Journal des actions')
            ->assertSee('Modification')
            ->assertSee('transferts')
            ->assertSee('125')
            ->assertSee('Ajout du transfert ID 3')
            ->assertSee('Modification du montant de caisse à 5000')
            ->assertSee('Info');
    }

    public function test_creator_columns_exist_on_all_requested_tables(): void
    {
        foreach ([
            'transferts',
            'expeditions',
            'conteneurs',
            'approcaisses',
            'depenses',
            'paymentcolis',
            'tarifexpeditions',
            'tauxtransferts',
        ] as $table) {
            $this->assertTrue(Schema::hasColumn($table, 'iduser'), "Missing iduser on {$table}");
        }
    }

    private function createUser(string $username, ?int $roleId = null): User
    {
        $userId = DB::table('users')->insertGetId([
            'name' => $username,
            'username' => $username,
            'email' => $username . '@example.test',
            'password' => bcrypt('password'),
            'actif' => true,
            'idrole' => $roleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::findOrFail($userId);
    }
}
