<?php

namespace Tests\Feature;

use App\Models\HelpdeskCategory;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpdeskTest extends TestCase
{
    use RefreshDatabase;

    public function test_helpdesk_index_page_loads(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('helpdesk.index'))->assertOk();
    }

    public function test_user_can_create_ticket(): void
    {
        $user = User::factory()->create();
        $category = HelpdeskCategory::create(['name' => 'IT Support', 'color' => '#ff0000']);

        $this->actingAs($user)
            ->post(route('helpdesk.store'), [
                'subject' => 'Cannot access email',
                'description' => 'I am unable to log in to my email account.',
                'category_id' => $category->id,
                'priority' => 'high',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('helpdesk_tickets', [
            'subject' => 'Cannot access email',
            'created_by' => $user->id,
        ]);
    }

    public function test_ticket_show_page_loads(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::create([
            'ticket_number' => 'HDT-0001',
            'subject' => 'Test Ticket',
            'description' => 'Test description',
            'status' => 'new',
            'priority' => 'medium',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('helpdesk.show', $ticket))
            ->assertOk()
            ->assertSee($ticket->subject);
    }

    public function test_admin_can_assign_ticket(): void
    {
        $admin = User::factory()->create();
        $agent = User::factory()->create();

        $ticket = HelpdeskTicket::create([
            'ticket_number' => 'HDT-0002',
            'subject' => 'Network Issue',
            'description' => 'Network is down',
            'status' => 'new',
            'priority' => 'urgent',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->post(route('helpdesk.assign', $ticket), [
                'assigned_to' => $agent->id,
            ])
            ->assertSessionHas('success');

        $ticket->refresh();
        $this->assertEquals($agent->id, $ticket->assigned_to);
    }

    public function test_admin_can_update_ticket_status(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::create([
            'ticket_number' => 'HDT-0003',
            'subject' => 'Login Issue',
            'description' => 'Cannot login',
            'status' => 'open',
            'priority' => 'medium',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('helpdesk.status', $ticket), ['status' => 'resolved'])
            ->assertSessionHas('success');

        $ticket->refresh();
        $this->assertEquals('resolved', $ticket->status);
        $this->assertNotNull($ticket->resolved_at);
    }

    public function test_user_can_respond_to_ticket(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::create([
            'ticket_number' => 'HDT-0004',
            'subject' => 'Printer Issue',
            'description' => 'Printer not working',
            'status' => 'new',
            'priority' => 'low',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('helpdesk.respond', $ticket), [
                'body' => 'I will check the printer.',
                'is_internal' => false,
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('helpdesk_responses', [
            'ticket_id' => $ticket->id,
            'body' => 'I will check the printer.',
        ]);
    }
}
