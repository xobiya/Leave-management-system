<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Leave Management') }} | {{ $title ?? 'Dashboard' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        class="font-sans antialiased bg-base-0 text-base-900"
        x-data="appShell()"
        :class="{ 'dark': darkMode }"
    >
        @php
            $panel = $panel ?? 'employee';
            $panelLabels = [
                'employee' => 'Employee',
                'manager' => 'Manager',
                'admin' => 'Admin',
            ];
            $icons = [
                'home' => '<path d="M4 10.5L12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z" />',
                'calendar' => '<path d="M8 3v3M16 3v3M4 10h16M6 6h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Z" />',
                'clipboard' => '<path d="M9 3h6v4H9z" /><path d="M7 7h10a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z" />',
                'users' => '<path d="M16 11a3 3 0 1 0-6 0" /><path d="M2 20a6 6 0 0 1 12 0" /><path d="M17 20a5 5 0 0 1 5 0" /><circle cx="16" cy="8" r="3" />',
                'settings' => '<path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z" /><path d="M19.4 12a7.4 7.4 0 0 0-.1-1l2-1.5-2-3.4-2.3.8a7 7 0 0 0-1.7-1L15 2h-4l-.3 2.9a7 7 0 0 0-1.7 1L6.7 5l-2 3.4 2 1.5a7.4 7.4 0 0 0-.1 1 7.4 7.4 0 0 0 .1 1l-2 1.5 2 3.4 2.3-.8a7 7 0 0 0 1.7 1L11 22h4l.3-2.9a7 7 0 0 0 1.7-1l2.3.8 2-3.4-2-1.5a7.4 7.4 0 0 0 .1-1Z" />',
                'chart' => '<path d="M4 19h16" /><path d="M7 16V9" /><path d="M12 16V5" /><path d="M17 16v-7" />',
                'bell' => '<path d="M18 15a4 4 0 0 1-8 0" /><path d="M18 15H6a2 2 0 0 1-2-2c0-4 2-7 6-8V4a2 2 0 1 1 4 0v1c4 1 6 4 6 8a2 2 0 0 1-2 2Z" />',
                'shield' => '<path d="M12 3 4 6v6c0 5 3.8 8 8 9 4.2-1 8-4 8-9V6l-8-3Z" />',
                'spark' => '<path d="M4 13l3 1 1 3 1-3 3-1-3-1-1-3-1 3-3 1Z" /><path d="M14 6l2 .7.7 2 .7-2 2-.7-2-.7-.7-2-.7 2-2 .7Z" />',
            ];
            $menus = [
                'employee' => [
                    ['label' => 'Overview', 'route' => 'employee.dashboard', 'icon' => 'home'],
                    ['label' => 'My Requests', 'route' => 'employee.requests', 'icon' => 'clipboard'],
                    ['label' => 'Calendar', 'route' => 'employee.calendar', 'icon' => 'calendar'],
                    ['label' => 'Notifications', 'route' => 'employee.notifications', 'icon' => 'bell'],
                ],
                'manager' => [
                    ['label' => 'Approval Hub', 'route' => 'manager.dashboard', 'icon' => 'home'],
                    ['label' => 'Team Availability', 'route' => 'manager.team', 'icon' => 'users'],
                    ['label' => 'Department Calendar', 'route' => 'manager.calendar', 'icon' => 'calendar'],
                    ['label' => 'Reports', 'route' => 'manager.reports', 'icon' => 'chart'],
                ],
                'admin' => [
                    ['label' => 'Control Center', 'route' => 'admin.dashboard', 'icon' => 'home'],
                    ['label' => 'Leave Types', 'route' => 'admin.leave-types', 'icon' => 'clipboard'],
                    ['label' => 'Leave Policies', 'route' => 'admin.leave-policies', 'icon' => 'spark'],
                    ['label' => 'Allocations', 'route' => 'admin.allocations', 'icon' => 'calendar'],
                    ['label' => 'Users & Roles', 'route' => 'admin.users', 'icon' => 'shield'],
                    ['label' => 'Settings', 'route' => 'admin.settings', 'icon' => 'settings'],
                ],
            ];
            $menuItems = $menus[$panel] ?? $menus['employee'];
        @endphp

        <div class="min-h-screen erp-backdrop">
            <div
                class="fixed inset-0 z-40 lg:hidden"
                x-show="sidebarOpen"
                x-transition.opacity
                x-cloak
            >
                <div class="absolute inset-0 bg-slate-950/60" @click="sidebarOpen = false"></div>
                <aside class="relative z-50 flex h-full w-72 flex-col gap-6 border-r border-base-200/20 bg-base-900 px-4 py-6 text-base-50 shadow-soft dark:border-base-200/30 dark:bg-base-0 dark:text-base-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-600 to-accent-500 text-white shadow-soft">
                                <span class="text-lg font-bold">XB</span>
                            </div>
                            <div class="text-sm">
                                <p class="font-display text-base-50 dark:text-base-900">Xobiya</p>
                                <p class="text-xs text-base-300 dark:text-base-500">{{ $panelLabels[$panel] ?? 'Employee' }} panel</p>
                            </div>
                        </div>
                        <button class="h-9 w-9 rounded-xl border border-base-200/20 bg-base-900/60 text-base-300 dark:border-base-200/30 dark:bg-base-50/10 dark:text-base-600" @click="sidebarOpen = false">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="flex flex-col gap-2">
                        <p class="px-3 text-xs font-semibold uppercase tracking-widest text-base-300 dark:text-base-500">Workspace</p>
                        @foreach ($menuItems as $item)
                            @php $isActive = request()->routeIs($item['route']); @endphp
                            <a href="{{ route($item['route']) }}" class="flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ $isActive ? 'bg-base-0/10 text-base-50 shadow-inset dark:bg-base-900/10 dark:text-base-900' : 'text-base-200 hover:bg-base-0/10 hover:text-base-50 dark:text-base-600 dark:hover:bg-base-900/10 dark:hover:text-base-900' }}">
                                <svg class="h-5 w-5 text-base-300 dark:text-base-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    {!! $icons[$item['icon']] ?? '' !!}
                                </svg>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </aside>
            </div>
            <div class="flex min-h-screen">
                <aside
                    data-tour="sidebar"
                    class="sticky top-0 hidden h-screen lg:flex flex-col gap-6 border-r border-base-200/20 bg-base-900 px-4 py-6 text-base-50 shadow-inset dark:border-base-200/30 dark:bg-base-0 dark:text-base-700"
                    :class="{ 'w-24': sidebarCollapsed, 'w-72': !sidebarCollapsed }"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-600 to-accent-500 text-white shadow-soft">
                                <span class="text-lg font-bold">XB</span>
                            </div>
                            <div class="text-sm" x-show="!sidebarCollapsed">
                                <p class="font-display text-base-50 dark:text-base-900">Xobiya</p>
                                <p class="text-xs text-base-300 dark:text-base-500">{{ $panelLabels[$panel] ?? 'Employee' }} panel</p>
                            </div>
                        </div>
                        <button
                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-base-200/20 bg-base-900/60 text-base-300 transition hover:text-base-50 dark:border-base-200/30 dark:bg-base-50/10 dark:text-base-600 dark:hover:text-base-900"
                            x-show="!sidebarCollapsed"
                            @click="sidebarCollapsed = true"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 6l-6 6 6 6" /></svg>
                        </button>
                    </div>

                    <button
                        class="flex h-9 w-full items-center justify-center gap-2 rounded-xl border border-base-200/20 bg-base-900/60 text-xs font-semibold text-base-300 transition hover:text-base-50 dark:border-base-200/30 dark:bg-base-50/10 dark:text-base-600 dark:hover:text-base-900"
                        x-show="sidebarCollapsed"
                        @click="sidebarCollapsed = false"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6" /></svg>
                        Expand
                    </button>

                    <div class="flex flex-col gap-2">
                        <p class="px-3 text-xs font-semibold uppercase tracking-widest text-base-300 dark:text-base-500" x-show="!sidebarCollapsed">Workspace</p>
                        @foreach ($menuItems as $item)
                            @php $isActive = request()->routeIs($item['route']); @endphp
                            <a
                                href="{{ route($item['route']) }}"
                                class="group flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition {{ $isActive ? 'bg-base-0/10 text-base-50 shadow-inset dark:bg-base-900/10 dark:text-base-900' : 'text-base-200 hover:bg-base-0/10 hover:text-base-50 dark:text-base-600 dark:hover:bg-base-900/10 dark:hover:text-base-900' }}"
                                :class="{ 'justify-center': sidebarCollapsed }"
                            >
                                <svg class="h-5 w-5 text-base-300 transition group-hover:text-base-50 dark:text-base-500 dark:group-hover:text-base-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    {!! $icons[$item['icon']] ?? '' !!}
                                </svg>
                                <span x-show="!sidebarCollapsed">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-auto">
                        <div class="rounded-2xl border border-base-200/20 bg-base-900/60 p-4 shadow-soft dark:border-base-200/30 dark:bg-base-50/10" x-show="!sidebarCollapsed">
                            <p class="text-xs font-semibold text-base-200 dark:text-base-700">Need help?</p>
                            <p class="mt-2 text-sm text-base-100 dark:text-base-600">Guided workflows and contextual tips are ready for your team.</p>
                            <button
                                class="mt-4 w-full rounded-xl border border-base-200/20 bg-base-900/60 px-3 py-2 text-xs font-semibold text-base-100 transition hover:bg-base-900/80 dark:border-base-200/30 dark:bg-base-50/10 dark:text-base-700 dark:hover:bg-base-50/20"
                                @click="startTour"
                            >
                                Launch guide
                            </button>
                        </div>
                    </div>
                </aside>

                <div class="flex flex-1 flex-col">
                    <header class="sticky top-0 z-20 border-b border-base-100/70 bg-base-0/90 px-4 py-4 shadow-inset backdrop-blur dark:border-base-200/30 dark:bg-base-0/20">
                        <div class="flex flex-wrap items-center gap-4">
                            <button
                                class="flex h-10 w-10 items-center justify-center rounded-xl border border-base-100/70 bg-base-0/80 text-base-500 shadow-inset transition hover:text-base-900 dark:border-base-200/30 dark:bg-base-50/10 lg:hidden"
                                @click="sidebarOpen = true"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16" /></svg>
                            </button>

                            <div class="flex-1">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-base-500">{{ $panelLabels[$panel] ?? 'Employee' }} workspace</p>
                                <h1 class="mt-1 text-2xl font-semibold text-base-900">{{ $title ?? 'Dashboard' }}</h1>
                            </div>

                            <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                                <div class="relative" data-tour="search">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-base-400">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7" /><path d="m20 20-3.5-3.5" /></svg>
                                    </span>
                                    <input class="erp-input pl-9" placeholder="Search people, requests, reports..." />
                                </div>
                                <button class="erp-button-ghost" data-tour="quick-apply">Quick apply</button>
                                <button
                                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-base-100/70 bg-base-0/80 text-base-500 shadow-inset transition hover:text-base-900 dark:border-base-200/30 dark:bg-base-50/10"
                                    @click="darkMode = !darkMode"
                                    :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
                                >
                                    <svg x-show="!darkMode" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="4" />
                                        <path d="M12 2v2" />
                                        <path d="M12 20v2" />
                                        <path d="M4.93 4.93l1.41 1.41" />
                                        <path d="M17.66 17.66l1.41 1.41" />
                                        <path d="M2 12h2" />
                                        <path d="M20 12h2" />
                                        <path d="M4.93 19.07l1.41-1.41" />
                                        <path d="M17.66 6.34l1.41-1.41" />
                                    </svg>
                                    <svg x-show="darkMode" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79Z" />
                                    </svg>
                                </button>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-xl px-2 py-1 transition hover:bg-base-50">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 text-brand-700">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                                        <div class="text-sm">
                                            <p class="font-semibold text-base-900">{{ Auth::user()->name }}</p>
                                            <p class="text-xs text-base-500">{{ Auth::user()->email }}</p>
                                        </div>
                                    </a>
                                    <x-dropdown align="right" width="48">
                                        <x-slot name="trigger">
                                            <button class="flex h-9 w-9 items-center justify-center rounded-xl border border-base-100/70 bg-base-0/70 text-base-500 transition hover:text-base-900 dark:border-base-200/30 dark:bg-base-50/10">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1" /><circle cx="12" cy="5" r="1" /><circle cx="12" cy="19" r="1" /></svg>
                                            </button>
                                        </x-slot>
                                        <x-slot name="content">
                                            <x-dropdown-link :href="route('profile.edit')">
                                                {{ __('Profile') }}
                                            </x-dropdown-link>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                                    {{ __('Log Out') }}
                                                </x-dropdown-link>
                                            </form>
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                            </div>
                        </div>
                    </header>

                    <main class="flex-1 px-4 py-6 lg:px-10 erp-reveal">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>

        <div x-show="tourOpen" x-cloak class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-slate-950/70" @click="tourOpen = false"></div>
            <div
                x-show="tourRect"
                class="absolute rounded-2xl border border-brand-400/60"
                :style="tourRect ? `top:${tourRect.top}px;left:${tourRect.left}px;width:${tourRect.width}px;height:${tourRect.height}px;box-shadow:0 0 0 9999px rgba(2,6,23,0.65)` : ''"
            ></div>
            <div
                class="absolute max-w-sm rounded-2xl border border-base-100/70 bg-base-0/95 p-5 shadow-soft backdrop-blur dark:border-base-200/40 dark:bg-base-0/10"
                :style="`top:${tourTooltip.top}px;left:${tourTooltip.left}px`"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="erp-label">Tour guide</p>
                        <h3 class="mt-2 text-lg font-semibold" x-text="tourSteps[tourStep].title"></h3>
                    </div>
                    <button class="text-base-400 hover:text-base-900" @click="tourOpen = false">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="mt-3 text-sm text-base-600" x-text="tourSteps[tourStep].body"></p>
                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs text-base-500">Step <span x-text="tourStep + 1"></span> of <span x-text="tourSteps.length"></span></span>
                    <div class="flex gap-2">
                        <button class="erp-button-ghost" type="button" @click="prevTour" :disabled="tourStep === 0">Back</button>
                        <button class="erp-button" type="button" @click="nextTour" x-text="tourStep === tourSteps.length - 1 ? 'Finish' : 'Next'"></button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
