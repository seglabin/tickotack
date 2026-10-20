<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CheckoutOrderTest extends TestCase
{
    use RefreshDatabase;

    private int $deliveryTypeId;
    private int $pickupTypeId;
    private int $articleId;

    protected function setUp(): void
    {
        parent::setUp();

        $categoryId = DB::table('categories')->insertGetId([
            'libelle' => 'Test',
            'visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->articleId = DB::table('articles')->insertGetId([
            'libelle' => 'Article de test',
            'prix' => 100,
            'reference' => 'TEST-ARTICLE',
            'idcategorie' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->deliveryTypeId = DB::table('elements')->insertGetId([
            'libelle' => 'Livraison à domicile',
            'nomtable' => 'typelivraison',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->pickupTypeId = DB::table('elements')->insertGetId([
            'libelle' => 'Moi même',
            'nomtable' => 'typelivraison',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_guest_can_place_a_cash_on_delivery_order_and_the_cart_is_cleared(): void
    {
        $response = $this->withSession([
            'panier' => [[
                'idproduit' => $this->articleId,
                'libelle' => 'Ancien libellé',
                'pu' => 1,
                'qte' => 2,
                'montant' => 2,
            ]],
        ])->post(route('checkout.place-order'), [
            'nom' => 'Client Test',
            'email' => 'client@example.test',
            'telephone' => '+229 90000000',
            'idtypelivraison' => $this->deliveryTypeId,
            'adresse' => 'Rue du Test',
            'ville' => 'Cotonou',
            'pays' => 'Bénin',
        ]);

        $commande = DB::table('commandes')->first();

        $this->assertNotNull($commande);
        $this->assertNotEmpty($commande->reference);
        $this->assertSame('paiement_a_la_livraison', $commande->methode_paiement);
        $this->assertEquals(200, $commande->sous_total);
        $this->assertEquals(2.5, $commande->frais_livraison);
        $this->assertEquals(36, $commande->taxe);
        $this->assertEquals(238.5, $commande->total);
        $this->assertDatabaseHas('paniers', [
            'idcmd' => $commande->id,
            'idarticle' => $this->articleId,
            'libelle' => 'Article de test',
            'qte' => 2,
            'pu' => 100,
        ]);
        $this->assertDatabaseHas('clients', [
            'id' => $commande->idclient,
            'email' => 'client@example.test',
        ]);
        $response->assertRedirect(route('order.confirmation', ['reference' => $commande->reference]));
        $response->assertSessionMissing('panier');

        $this->get(route('order.confirmation', ['reference' => $commande->reference]))
            ->assertOk()
            ->assertSee('Votre commande est enregistrée')
            ->assertSee($commande->reference)
            ->assertSee('Article de test');
    }

    public function test_pickup_does_not_require_a_delivery_address_or_charge_shipping(): void
    {
        $this->withSession([
            'panier' => [[
                'idproduit' => $this->articleId,
                'qte' => 1,
            ]],
        ])->post(route('checkout.place-order'), [
            'nom' => 'Client Retrait',
            'email' => 'retrait@example.test',
            'telephone' => '+229 90000001',
            'idtypelivraison' => $this->pickupTypeId,
        ])->assertRedirect();

        $this->assertDatabaseHas('commandes', [
            'emailclient' => 'retrait@example.test',
            'frais_livraison' => 0,
            'adresse_livraison' => null,
            'total' => 118,
        ]);
    }

    public function test_delivery_address_is_required_before_order_submission(): void
    {
        $this->withSession([
            'panier' => [[
                'idproduit' => $this->articleId,
                'qte' => 1,
            ]],
        ])->post(route('checkout.place-order'), [
            'nom' => 'Client Test',
            'email' => 'adresse@example.test',
            'telephone' => '+229 90000002',
            'idtypelivraison' => $this->deliveryTypeId,
        ])->assertSessionHasErrors(['adresse', 'ville', 'pays']);

        $this->assertDatabaseCount('commandes', 0);
    }

    public function test_checkout_redirects_to_the_cart_when_it_is_empty(): void
    {
        $this->get(route('checkout'))
            ->assertRedirect(route('cart'))
            ->assertSessionHas('error', 'Votre panier est vide.');
    }
}
