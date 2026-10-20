<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CategoryListingTest extends TestCase
{
    use RefreshDatabase;

    private int $categoryId;
    private int $firstBrandId;
    private int $secondBrandId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryId = DB::table('categories')->insertGetId([
            'libelle' => 'Papeterie',
            'visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->firstBrandId = DB::table('marques')->insertGetId([
            'libelle' => 'Marque A',
            'visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->secondBrandId = DB::table('marques')->insertGetId([
            'libelle' => 'Marque B',
            'visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (range(1, 28) as $index) {
            DB::table('articles')->insert([
                'libelle' => sprintf('Produit %02d', $index),
                'prix' => $index * 10,
                'reference' => sprintf('CATEGORY-%02d', $index),
                'idcategorie' => $this->categoryId,
                'idmarque' => $index % 2 === 0 ? $this->firstBrandId : $this->secondBrandId,
                'created_at' => now()->subDays(15 - $index),
                'updated_at' => now(),
            ]);
        }
    }

    public function test_price_sort_is_applied_to_the_catalog_query(): void
    {
        $response = $this->get(route('category', ['slug' => $this->categoryId, 'sort' => 'priceAsc']));

        $response->assertOk()->assertViewHas('products', function ($products) {
            return $products->total() === 28
                && (float) $products->first()->prix === 10.0
                && (float) $products->last()->prix === 120.0;
        });

        $descending = $this->get(route('category', ['slug' => $this->categoryId, 'sort' => 'priceDesc']));
        $descending->assertOk()->assertViewHas('products', function ($products) {
            return (float) $products->first()->prix === 280.0;
        });
    }

    public function test_name_sort_orders_products_alphabetically(): void
    {
        $response = $this->get(route('category', ['slug' => $this->categoryId, 'sort' => 'name']));

        $response->assertOk()->assertViewHas('products', function ($products) {
            return $products->first()->libelle === 'Produit 01';
        });
    }

    public function test_brand_and_price_filters_are_combined_on_the_server(): void
    {
        $response = $this->get(route('category', [
            'slug' => $this->categoryId,
            'brand' => $this->firstBrandId,
            'min_price' => 50,
            'max_price' => 100,
            'sort' => 'priceAsc',
        ]));

        $response->assertOk()->assertViewHas('products', function ($products) {
            return $products->total() === 3
                && $products->every(fn ($product) => (int) $product->idmarque === $this->firstBrandId
                    && (float) $product->prix >= 50
                    && (float) $product->prix <= 100);
        });
        $response->assertViewHas('filters', fn ($filters) => $filters['brand'] === (string) $this->firstBrandId);
    }

    public function test_pagination_limits_results_and_preserves_active_filters(): void
    {
        $response = $this->get(route('category', [
            'slug' => $this->categoryId,
            'brand' => $this->firstBrandId,
            'sort' => 'priceAsc',
            'page' => 2,
        ]));

        $response->assertOk()->assertViewHas('products', function ($products) {
            return $products->total() === 14
                && $products->currentPage() === 2
                && $products->count() === 2;
        });
    }

    public function test_pagination_is_shown_for_more_than_one_page_and_keeps_filter_parameters(): void
    {
        $response = $this->get(route('category', [
            'slug' => $this->categoryId,
            'brand' => $this->firstBrandId,
            'sort' => 'priceAsc',
        ]));

        $response->assertOk();
        $response->assertSee('brand=' . $this->firstBrandId, false);
        $response->assertSee('sort=priceAsc', false);
    }

    public function test_invalid_price_range_is_rejected(): void
    {
        $response = $this->get(route('category', [
            'slug' => $this->categoryId,
            'min_price' => 100,
            'max_price' => 20,
        ]));

        $response->assertRedirect();
        $response->assertSessionHasErrors('max_price');
    }
}
