<x-layouts.erp :title="'Gamification Dashboard'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gamification Dashboard</h1>
            <p class="text-sm text-gray-500 mt-0.5">Badges, challenges, and leaderboard overview.</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Badges</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_badges'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Active Challenges</p>
            <p class="text-2xl font-bold text-indigo-600">{{ $stats['active_challenges'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Badges Awarded</p>
            <p class="text-2xl font-bold text-orange-600">{{ $stats['total_assignments'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Users with Karma</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['users_with_karma'] }}</p>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('gamification.badges') }}" class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-indigo-200 transition group">
            <div class="w-12 h-12 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center mb-4 group-hover:bg-indigo-200 transition">
                <i class="ph ph-certificate text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Badges</h3>
            <p class="text-sm text-gray-500">Create and assign achievement badges to users.</p>
        </a>
        <a href="{{ route('gamification.challenges') }}" class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-indigo-200 transition group">
            <div class="w-12 h-12 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center mb-4 group-hover:bg-orange-200 transition">
                <i class="ph ph-trophy text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Challenges</h3>
            <p class="text-sm text-gray-500">Set up time-bound challenges with reward badges.</p>
        </a>
        <a href="{{ route('gamification.leaderboard') }}" class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-indigo-200 transition group">
            <div class="w-12 h-12 rounded-lg bg-green-100 text-green-600 flex items-center justify-center mb-4 group-hover:bg-green-200 transition">
                <i class="ph ph-ranking text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Leaderboard</h3>
            <p class="text-sm text-gray-500">View users ranked by karma score.</p>
        </a>
    </div>
</x-layouts.erp>
