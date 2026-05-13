<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockLevel;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_index_page_loads(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('inventory.index'))
            ->assertOk();
    }

    public function test_admin_can_create_product(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('inventory.store'), [
                'name' => 'Test Product',
                'code' => 'PROD-001',
                'type' => 'stockable',
                'cost' => 50,
                'price' => 100,
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('products', ['code' => 'PROD-001']);
    }

    public function test_admin_can_adjust_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Widget',
            'code' => 'WDG-001',
            'type' => 'stockable',
            'cost' => 10,
            'price' => 25,
        ]);
        $warehouse = Warehouse::create([
            'name' => 'Main Warehouse',
            'code' => 'WH-MAIN',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->post(route('inventory.adjust'), [
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'quantity' => 100,
                'type' => 'incoming',
            ])
            ->assertSessionHas('success');

        $stockLevel = StockLevel::where('product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->first();

        $this->assertNotNull($stockLevel);
        $this->assertEquals(100, $stockLevel->quantity);
    }

    public function test_inventory_show_page_loads(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Widget',
            'code' => 'WDG-001',
            'type' => 'stockable',
            'cost' => 10,
            'price' => 25,
        ]);

        $this->actingAs($user)
            ->get(route('inventory.show', $product))
            ->assertOk()
            ->assertSee($product->name);
    }
}
