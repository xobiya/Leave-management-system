<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_index_page_loads(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('projects.index'))->assertOk();
    }

    public function test_create_page_loads(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('projects.create'))->assertOk();
    }

    public function test_admin_can_create_project(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('projects.store'), [
                'name' => 'New Project',
                'manager_id' => $user->id,
                'status' => 'draft',
            ])
            ->assertRedirect(route('projects.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('projects', ['name' => 'New Project']);
    }

    public function test_project_show_page_loads(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'name' => 'Test Project',
            'code' => 'PRJ/TES/001',
            'manager_id' => $user->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee($project->name);
    }

    public function test_admin_can_add_task_to_project(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'name' => 'Test Project',
            'code' => 'PRJ/TES/001',
            'manager_id' => $user->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->post(route('projects.tasks.store', $project), [
                'title' => 'Design Homepage',
                'assigned_to' => $user->id,
                'deadline' => now()->addWeek()->toDateString(),
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', ['title' => 'Design Homepage', 'project_id' => $project->id]);
    }

    public function test_admin_can_log_time_on_task(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'name' => 'Test',
            'code' => 'PRJ/TST/001',
            'manager_id' => $user->id,
            'status' => 'active',
        ]);

        $task = Task::create([
            'project_id' => $project->id,
            'title' => 'Design',
            'assigned_to' => $user->id,
            'status' => 'progress',
        ]);

        $this->actingAs($user)
            ->post(route('tasks.log-time', $task), [
                'hours' => 5,
                'date' => now()->toDateString(),
                'description' => 'Worked on design',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('timesheets', ['task_id' => $task->id, 'hours' => 5]);
    }

    public function test_admin_can_update_task_status(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'name' => 'Test',
            'code' => 'PRJ/TST/001',
            'manager_id' => $user->id,
            'status' => 'active',
        ]);

        $task = Task::create([
            'project_id' => $project->id,
            'title' => 'Design',
            'assigned_to' => $user->id,
            'status' => 'todo',
        ]);

        $this->actingAs($user)
            ->post(route('tasks.status', $task), ['status' => 'progress'])
            ->assertSessionHas('success');

        $task->refresh();
        $this->assertEquals('progress', $task->status);
    }
}
