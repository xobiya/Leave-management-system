<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_index_page_loads(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('sales.index'))->assertOk();
    }

    public function test_create_page_loads(): void
    {
        $user = User::factory()->create();
        Customer::create(['name' => 'Test Corp', 'email' => 'test@corp.com', 'type' => 'customer', 'status' => 'active']);
        Product::create(['name' => 'Widget', 'code' => 'WDG', 'type' => 'stockable', 'cost' => 10, 'price' => 25]);

        $this->actingAs($user)->get(route('sales.create'))->assertOk();
    }

    public function test_admin_can_create_sales_order(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create(['name' => 'Test Corp', 'email' => 'test@corp.com', 'type' => 'customer', 'status' => 'active']);
        $product = Product::create(['name' => 'Widget', 'code' => 'WDG', 'type' => 'stockable', 'cost' => 10, 'price' => 25]);

        $this->actingAs($user)
            ->post(route('sales.store'), [
                'customer_id' => $customer->id,
                'date' => now()->toDateString(),
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 5, 'unit_price' => 25],
                ],
            ])
            ->assertRedirect(route('sales.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('sales_orders', [
            'customer_id' => $customer->id,
            'status' => 'draft',
        ]);
    }

    public function test_admin_can_confirm_sales_order(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create(['name' => 'Test Corp', 'email' => 'test@corp.com', 'type' => 'customer', 'status' => 'active']);

        $order = SalesOrder::create([
            'customer_id' => $customer->id,
            'code' => 'SO/2026/001',
            'date' => now(),
            'total_amount' => 100,
            'status' => 'draft',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('sales.confirm', $order))
            ->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('confirmed', $order->status);
    }
}
