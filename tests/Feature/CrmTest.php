<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmTest extends TestCase
{
    use RefreshDatabase;

    public function test_crm_index_page_loads(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('crm.index'))->assertOk();
    }

    public function test_pipeline_page_loads(): void
    {
        $user = User::factory()->create();
        Customer::create(['name' => 'Test Corp', 'email' => 'test@corp.com', 'type' => 'customer']);

        $this->actingAs($user)->get(route('crm.pipeline'))->assertOk();
    }

    public function test_admin_can_create_customer(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('crm.customers.store'), [
                'name' => 'Acme Corp',
                'email' => 'contact@acme.com',
                'type' => 'customer',
                'phone' => '1234567890',
                'company' => 'Acme Inc',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('customers', ['email' => 'contact@acme.com']);
    }

    public function test_admin_can_create_opportunity(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create(['name' => 'Test Corp', 'email' => 'test@corp.com', 'type' => 'customer']);

        $this->actingAs($user)
            ->post(route('crm.opportunities.store'), [
                'customer_id' => $customer->id,
                'title' => 'Big Deal',
                'expected_revenue' => 50000,
                'closing_date' => now()->addMonth()->toDateString(),
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('opportunities', ['title' => 'Big Deal', 'stage' => 'new']);
    }

    public function test_opportunity_stage_can_be_updated(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create(['name' => 'Test Corp', 'email' => 'test@corp.com', 'type' => 'customer']);
        $opportunity = Opportunity::create([
            'customer_id' => $customer->id,
            'title' => 'Big Deal',
            'expected_revenue' => 50000,
            'stage' => 'new',
            'assigned_to' => $user->id,
            'probability' => 10,
        ]);

        $this->actingAs($user)
            ->post(route('crm.opportunities.stage', $opportunity), ['stage' => 'qualified'])
            ->assertSessionHas('success');

        $opportunity->refresh();
        $this->assertEquals('qualified', $opportunity->stage);
        $this->assertEquals(30, $opportunity->probability);
    }
}
