<x-layouts.erp :title="'Notifications'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            <p class="text-sm text-gray-500 mt-0.5">System alerts and updates</p>
        </div>
        <div class="flex gap-2">
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">Mark all read</button>
            </form>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.3fr_1fr]">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            @if($notifications->count())
                <div class="space-y-4">
                    @foreach($notifications as $notification)
                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 {{ is_null($notification->read_at) ? 'border-l-4 border-l-indigo-500' : '' }}">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-gray-900">{{ $notification->title }}</p>
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ is_null($notification->read_at) ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                    {{ is_null($notification->read_at) ? 'New' : 'Read' }}
                                </span>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">{{ $notification->body }}</p>
                            <div class="mt-3 flex items-center justify-between">
                                <p class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                                @if(is_null($notification->read_at))
                                    <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Mark read</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="ph ph-bell text-4xl text-gray-300"></i>
                    <p class="mt-4 text-gray-500 text-sm">No notifications yet.</p>
                </div>
            @endif
        </div>

        <aside class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Summary</p>
                <h3 class="mt-2 text-lg font-bold text-gray-900">Notification stats</h3>
                <div class="mt-4 space-y-3 text-sm text-gray-600">
                    <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <p class="font-semibold text-gray-900">{{ $notifications->total() }}</p>
                        <p class="text-xs text-gray-500">Total notifications</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <p class="font-semibold text-gray-900">{{ $notifications->filter(fn($n) => is_null($n->read_at))->count() }}</p>
                        <p class="text-xs text-gray-500">Unread</p>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</x-layouts.erp>