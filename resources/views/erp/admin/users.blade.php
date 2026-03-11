<x-app-layout title="Users & Roles" panel="admin">
    <div class="grid gap-6">
        <div class="erp-card p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="erp-label">Directory</p>
                    <h2 class="mt-2 text-xl font-semibold">User management</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <input class="erp-input" placeholder="Search users" />
                    <button class="erp-button">Add user</button>
                </div>
            </div>
            <table class="erp-table mt-6">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Access</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->department?->name ?? 'Unassigned' }}</td>
                            <td>{{ $user->roles->first()?->name ?? 'None' }}</td>
                            <td>
                                <span class="erp-badge {{ $user->status === 'active' ? 'erp-badge-success' : 'erp-badge-warning' }}">
                                    {{ ucfirst($user->status ?? 'active') }}
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.users.assign-role', $user) }}" class="flex gap-2">
                                    @csrf
                                    <select name="role_id" class="erp-input">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ $user->roles->first()?->id === $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="erp-button-ghost" type="submit">Update</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-xs text-base-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="erp-card p-6">
            <p class="erp-label">Role & permission UI</p>
            <h3 class="mt-2 text-lg font-semibold">Access control editor</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl border border-base-100/70 bg-base-0/70 p-4">
                    <p class="font-semibold text-base-900">Manager</p>
                    <p class="text-xs text-base-500">Approve requests, view team coverage.</p>
                    <button class="erp-button-ghost mt-4 w-full">Edit permissions</button>
                </div>
                <div class="rounded-2xl border border-base-100/70 bg-base-0/70 p-4">
                    <p class="font-semibold text-base-900">Employee</p>
                    <p class="text-xs text-base-500">Submit requests, track balances.</p>
                    <button class="erp-button-ghost mt-4 w-full">Edit permissions</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
