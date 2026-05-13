<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_index_page_loads(): void
    {
        $user = User::factory()->create();
        Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('assets.index'))
            ->assertOk();
    }

    public function test_admin_can_create_asset(): void
    {
        $user = User::factory()->create();
        $category = AssetCategory::create(['name' => 'IT Equipment']);

        $this->actingAs($user)
            ->post(route('assets.store'), [
                'name' => 'MacBook Pro',
                'code' => 'AST-001',
                'asset_category_id' => $category->id,
                'serial_number' => 'SN123456',
                'purchase_cost' => 2500,
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('assets', ['code' => 'AST-001']);
    }

    public function test_admin_can_assign_asset(): void
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'status' => 'active',
        ]);

        $category = AssetCategory::create(['name' => 'IT Equipment']);
        $asset = Asset::create([
            'name' => 'MacBook Pro',
            'code' => 'AST-001',
            'asset_category_id' => $category->id,
            'status' => 'available',
        ]);

        $this->actingAs($user)
            ->post(route('assets.assign', $asset), [
                'employee_id' => $employee->id,
            ])
            ->assertSessionHas('success');

        $asset->refresh();
        $this->assertEquals('assigned', $asset->status);
        $this->assertEquals($employee->id, $asset->employee_id);
    }

    public function test_admin_can_return_asset(): void
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP001',
            'status' => 'active',
        ]);

        $category = AssetCategory::create(['name' => 'IT Equipment']);
        $asset = Asset::create([
            'name' => 'MacBook Pro',
            'code' => 'AST-001',
            'asset_category_id' => $category->id,
            'status' => 'assigned',
            'employee_id' => $employee->id,
        ]);

        $this->actingAs($user)
            ->post(route('assets.return', $asset))
            ->assertSessionHas('success');

        $asset->refresh();
        $this->assertEquals('available', $asset->status);
        $this->assertNull($asset->employee_id);
    }

    public function test_asset_show_page_loads(): void
    {
        $user = User::factory()->create();
        $category = AssetCategory::create(['name' => 'IT Equipment']);
        $asset = Asset::create([
            'name' => 'MacBook Pro',
            'code' => 'AST-001',
            'asset_category_id' => $category->id,
            'status' => 'available',
        ]);

        $this->actingAs($user)
            ->get(route('assets.show', $asset))
            ->assertOk()
            ->assertSee($asset->name);
    }
}
