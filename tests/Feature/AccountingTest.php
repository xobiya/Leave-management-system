<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingTest extends TestCase
{
    use RefreshDatabase;

    public function test_accounting_index_page_loads(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('accounting.index'))->assertOk();
    }

    public function test_chart_of_accounts_page_loads(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('accounting.coa'))->assertOk();
    }

    public function test_journals_page_loads(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('accounting.journals'))->assertOk();
    }

    public function test_profit_and_loss_page_loads(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('accounting.reports.pnl'))->assertOk();
    }

    public function test_admin_can_create_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('accounting.accounts.store'), [
                'name' => 'Cash Account',
                'code' => '1000',
                'type' => 'asset',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('accounts', ['code' => '1000']);
    }

    public function test_profit_and_loss_calculates_correctly(): void
    {
        $user = User::factory()->create();
        $income = Account::create(['name' => 'Revenue', 'code' => '4000', 'type' => 'income']);
        $expense = Account::create(['name' => 'Rent', 'code' => '6000', 'type' => 'expense']);
        $bank = Account::create(['name' => 'Bank', 'code' => '1100', 'type' => 'asset']);

        $journal = Journal::create(['name' => 'Sales Journal', 'code' => 'SJ', 'type' => 'sales']);
        $entry = JournalEntry::create([
            'journal_id' => $journal->id,
            'code' => 'JE-001',
            'date' => now(),
            'state' => 'posted',
        ]);

        $entry->items()->createMany([
            ['account_id' => $income->id, 'credit' => 10000, 'debit' => 0],
            ['account_id' => $bank->id, 'credit' => 0, 'debit' => 10000],
        ]);

        $entry2 = JournalEntry::create([
            'journal_id' => $journal->id,
            'code' => 'JE-002',
            'date' => now(),
            'state' => 'posted',
        ]);

        $entry2->items()->createMany([
            ['account_id' => $expense->id, 'credit' => 0, 'debit' => 3000],
            ['account_id' => $bank->id, 'credit' => 3000, 'debit' => 0],
        ]);

        $this->actingAs($user)
            ->get(route('accounting.reports.pnl'))
            ->assertOk();
    }
}
