<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcurementTest extends TestCase
{
    use RefreshDatabase;

    public function test_procurement_index_page_loads(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('procurement.index'))->assertOk();
    }

    public function test_create_page_loads(): void
    {
        $user = User::factory()->create();
        Vendor::create(['name' => 'Test Vendor', 'code' => 'V001', 'is_active' => true]);
        Product::create(['name' => 'Widget', 'code' => 'WDG', 'type' => 'stockable', 'cost' => 10, 'price' => 25]);
        Warehouse::create(['name' => 'Main', 'code' => 'WH', 'is_active' => true]);

        $this->actingAs($user)->get(route('procurement.create'))->assertOk();
    }

    public function test_admin_can_create_purchase_order(): void
    {
        $user = User::factory()->create();
        $vendor = Vendor::create(['name' => 'Test Vendor', 'code' => 'V001', 'is_active' => true]);
        $product = Product::create(['name' => 'Widget', 'code' => 'WDG', 'type' => 'stockable', 'cost' => 10, 'price' => 25]);
        $warehouse = Warehouse::create(['name' => 'Main', 'code' => 'WH', 'is_active' => true]);

        $this->actingAs($user)
            ->post(route('procurement.store'), [
                'vendor_id' => $vendor->id,
                'warehouse_id' => $warehouse->id,
                'date' => now()->toDateString(),
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 10, 'unit_price' => 15],
                ],
            ])
            ->assertRedirect(route('procurement.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('purchase_orders', [
            'vendor_id' => $vendor->id,
            'status' => 'draft',
        ]);
    }

    public function test_admin_can_receive_goods(): void
    {
        $user = User::factory()->create();
        $product = Product::create(['name' => 'Widget', 'code' => 'WDG', 'type' => 'stockable', 'cost' => 10, 'price' => 25]);
        $warehouse = Warehouse::create(['name' => 'Main', 'code' => 'WH', 'is_active' => true]);
        $vendor = Vendor::create(['name' => 'Test Vendor', 'code' => 'V001', 'is_active' => true]);

        $order = PurchaseOrder::create([
            'vendor_id' => $vendor->id,
            'warehouse_id' => $warehouse->id,
            'code' => 'PO/2026/001',
            'date' => now(),
            'status' => 'draft',
            'total_amount' => 150,
            'created_by' => $user->id,
        ]);

        $order->lines()->create([
            'product_id' => $product->id,
            'quantity' => 10,
            'unit_price' => 15,
            'subtotal' => 150,
        ]);

        $this->actingAs($user)
            ->post(route('procurement.receive', $order))
            ->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('received', $order->status);
    }
}
