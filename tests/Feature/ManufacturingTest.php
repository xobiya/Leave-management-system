<?php

namespace Tests\Feature;

use App\Models\Bom;
use App\Models\BomLine;
use App\Models\ManufacturingOrder;
use App\Models\Product;
use App\Models\StockLevel;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManufacturingTest extends TestCase
{
    use RefreshDatabase;

    public function test_manufacturing_index_page_loads(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('manufacturing.index'))->assertOk();
    }

    public function test_create_page_loads(): void
    {
        $user = User::factory()->create();
        Product::create(['name' => 'Widget', 'code' => 'WDG', 'type' => 'stockable', 'cost' => 10, 'price' => 25]);
        Warehouse::create(['name' => 'Main', 'code' => 'WH', 'is_active' => true]);

        $this->actingAs($user)->get(route('manufacturing.create'))->assertOk();
    }

    public function test_admin_can_create_manufacturing_order(): void
    {
        $user = User::factory()->create();
        $product = Product::create(['name' => 'Finished Widget', 'code' => 'FWDG', 'type' => 'stockable', 'cost' => 50, 'price' => 100]);
        $component = Product::create(['name' => 'Widget Part', 'code' => 'WGPT', 'type' => 'stockable', 'cost' => 10, 'price' => 20]);
        $warehouse = Warehouse::create(['name' => 'Main', 'code' => 'WH', 'is_active' => true]);
        $bom = Bom::create(['product_id' => $product->id, 'code' => 'BOM-FWDG', 'quantity' => 1]);
        BomLine::create(['bom_id' => $bom->id, 'product_id' => $component->id, 'quantity' => 2]);

        $this->actingAs($user)
            ->post(route('manufacturing.store'), [
                'product_id' => $product->id,
                'bom_id' => $bom->id,
                'quantity' => 1,
                'warehouse_id' => $warehouse->id,
            ])
            ->assertRedirect(route('manufacturing.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('manufacturing_orders', [
            'product_id' => $product->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_admin_can_complete_manufacturing_order(): void
    {
        $user = User::factory()->create();
        $product = Product::create(['name' => 'Finished Widget', 'code' => 'FWDG', 'type' => 'stockable', 'cost' => 50, 'price' => 100]);
        $component = Product::create(['name' => 'Widget Part', 'code' => 'WGPT', 'type' => 'stockable', 'cost' => 10, 'price' => 20]);
        $warehouse = Warehouse::create(['name' => 'Main', 'code' => 'WH', 'is_active' => true]);
        $bom = Bom::create(['product_id' => $product->id, 'code' => 'BOM-FWDG', 'quantity' => 1]);
        BomLine::create(['bom_id' => $bom->id, 'product_id' => $component->id, 'quantity' => 2]);

        StockLevel::create([
            'product_id' => $component->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 100,
        ]);

        $order = ManufacturingOrder::create([
            'code' => 'MO/2026/001',
            'product_id' => $product->id,
            'bom_id' => $bom->id,
            'quantity' => 1,
            'warehouse_id' => $warehouse->id,
            'status' => 'confirmed',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('manufacturing.complete', $order))
            ->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('done', $order->status);
    }
}
