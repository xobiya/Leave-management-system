<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'ERP' }} | {{ config('app.name', 'XobiyaHR') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        .sidebar-brand { background: #0f172a; }
        .module-active { 
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); 
            color: white; 
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }
        .submenu-active { color: #4f46e5; font-weight: 800; border-bottom: 2px solid #4f46e5; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .sidebar-link-hover:hover { background: rgba(255,255,255,0.05); transform: translateX(4px); }
        .glass-panel { background: rgba(255,255,255,0.8); backdrop-filter: blur(12px); }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-900" x-data="{ sidebarOpen: true }">

    @php
        $navigation = [
            'WORKFORCE' => [
                'icon' => 'ph-users-three',
                'main_route' => 'employees.index',
                'children' => [
                    ['route' => 'employees.index',   'label' => 'Employees'],
                    ['route' => 'hr.dashboard',      'label' => 'HR Analytics'],
                    ['route' => 'positions.index',   'label' => 'Job Positions'],
                    ['route' => 'attendance.index',  'label' => 'Attendance Records'],
                    ['route' => 'attendance.my',     'label' => 'My Attendance'],
                    ['route' => 'payroll.index',     'label' => 'Payroll Management'],
                    ['route' => 'payroll.runs',      'label' => 'Payroll Batches'],
                    ['route' => 'payroll.my',        'label' => 'My Payslips'],
                ]
            ],
            'TIME OFF' => [
                'icon' => 'ph-calendar-blank',
                'main_route' => 'employee.dashboard',
                'children' => [
                    ['route' => 'employee.dashboard',  'label' => 'Personal Time Off'],
                    ['route' => 'manager.dashboard',   'label' => 'Manager Approvals'],
                    ['route' => 'admin.leave-types',   'label' => 'Leave Types'],
                    ['route' => 'admin.leave-policies','label' => 'Leave Policies'],
                    ['route' => 'admin.allocations',   'label' => 'Balance Allocations'],
                    ['route' => 'admin.accrual-plans', 'label' => 'Accrual Rules'],
                ]
            ],
            'LOGISTICS' => [
                'icon' => 'ph-truck',
                'main_route' => 'inventory.index',
                'children' => [
                    ['route' => 'inventory.index',         'label' => 'Inventory Master'],
                    ['route' => 'inventory.lots',          'label' => 'Lots & Serials'],
                    ['route' => 'inventory.batches',       'label' => 'Stock Operations'],
                    ['route' => 'inventory.landed-costs',  'label' => 'Landed Costs'],
                    ['route' => 'procurement.index',       'label' => 'Purchase Orders'],
                    ['route' => 'procurement.agreements',  'label' => 'Supplier Agreements'],
                    ['route' => 'procurement.requisitions','label' => 'Purchase Requests'],
                ]
            ],
            'SALES & CRM' => [
                'icon' => 'ph-funnel',
                'main_route' => 'crm.index',
                'children' => [
                    ['route' => 'crm.index',               'label' => 'Sales Pipeline'],
                    ['route' => 'crm.stages',              'label' => 'Pipeline Stages'],
                    ['route' => 'crm.teams',               'label' => 'Sales Teams'],
                    ['route' => 'sales.index',             'label' => 'Sales Contracts'],
                ]
            ],
            'FINANCE' => [
                'icon' => 'ph-bank',
                'main_route' => 'accounting.index',
                'children' => [
                    ['route' => 'accounting.index',        'label' => 'General Ledger'],
                    ['route' => 'accounting.coa',          'label' => 'Chart of Accounts'],
                    ['route' => 'accounting.journals',     'label' => 'Financial Journals'],
                    ['route' => 'accounting.reports.pnl',  'label' => 'Financial Performance'],
                    ['route' => 'accounting.invoices',     'label' => 'Customer Invoices'],
                    ['route' => 'accounting.taxes',        'label' => 'Tax Configurations'],
                    ['route' => 'assets.index',            'label' => 'Asset Register'],
                    ['route' => 'assets.my',               'label' => 'My Equipment'],
                    ['route' => 'assets.maintenance',      'label' => 'Asset Maintenance'],
                ]
            ],
            'OPERATIONS' => [
                'icon' => 'ph-factory',
                'main_route' => 'manufacturing.index',
                'children' => [
                    ['route' => 'manufacturing.index',     'label' => 'Manufacturing Orders'],
                    ['route' => 'manufacturing.boms',      'label' => 'Bill of Materials'],
                    ['route' => 'projects.index',          'label' => 'Project Portfolio'],
                    ['route' => 'repair.index',            'label' => 'Repair Management'],
                    ['route' => 'fleet.index',             'label' => 'Vehicle Fleet'],
                    ['route' => 'maintenance.index',       'label' => 'Equipment Maintenance'],
                ]
            ],
            'SERVICES' => [
                'icon' => 'ph-headset',
                'main_route' => 'helpdesk.index',
                'children' => [
                    ['route' => 'helpdesk.index',          'label' => 'Service Desk'],
                    ['route' => 'expenses.index',          'label' => 'Expense Management'],
                    ['route' => 'expenses.my',             'label' => 'My Expenses'],
                    ['route' => 'skills.index',            'label' => 'Skills Directory'],
                    ['route' => 'skills.employees',        'label' => 'Employee Skills'],
                    ['route' => 'recruitment.index',       'label' => 'Recruitment'],
                    ['route' => 'recruitment.applications','label' => 'Job Applications'],
                    ['route' => 'gamification.index',      'label' => 'Gamification'],
                    ['route' => 'lunch.index',             'label' => 'Catering Management'],
                ]
            ],
            'SYSTEM' => [
                'icon' => 'ph-gear-six',
                'main_route' => 'admin.dashboard',
                'children' => [
                    ['route' => 'admin.dashboard',         'label' => 'Control Panel'],
                    ['route' => 'admin.users',             'label' => 'Users & Security'],
                    ['route' => 'admin.roles.index',       'label' => 'Roles & Permissions'],
                    ['route' => 'companies.index',         'label' => 'Legal Entities'],
                ]
            ]
        ];

        // Role-aware TIME OFF main route
        $user = auth()->user();
        $timeOffMainRoute = 'employee.dashboard';
        if ($user && ($user->hasRole('super_admin') || $user->hasRole('admin'))) {
            $timeOffMainRoute = 'admin.leave-types';
        } elseif ($user && $user->hasRole('manager')) {
            $timeOffMainRoute = 'manager.dashboard';
        }
        $navigation['TIME OFF']['main_route'] = $timeOffMainRoute;

        // Find Active Module and Children
        $activeModuleName = 'DASHBOARD';
        $activeModuleChildren = [];
        $isHome = request()->routeIs('dashboard');

        foreach($navigation as $name => $module) {
            $isActive = collect($module['children'])->contains(fn($c) => request()->routeIs($c['route']) || request()->routeIs(str_replace('.index', '.*', $c['route'])));
            if($isActive) {
                $activeModuleName = $name;
                $activeModuleChildren = $module['children'];
                break;
            }
        }
    @endphp

    {{-- ─── MODULE SIDEBAR ─────────────────────────────────────────────────── --}}
    <aside class="fixed inset-y-0 left-0 z-50 flex flex-col sidebar-brand text-white transition-all duration-300 shadow-2xl"
           :class="sidebarOpen ? 'w-64' : 'w-20'">
        
        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 py-8 border-b border-white/5 cursor-pointer" @click="sidebarOpen = !sidebarOpen">
            <div class="w-10 h-10 flex-shrink-0 rounded-xl bg-indigo-600 flex items-center justify-center">
                <i class="ph-fill ph-cube text-white text-xl"></i>
            </div>
            <div x-show="sidebarOpen" x-transition class="overflow-hidden">
                <h1 class="font-bold text-lg tracking-tighter whitespace-nowrap uppercase text-white">Xobiya<span class="text-indigo-400">HR</span></h1>
            </div>
        </div>

        {{-- Primary Modules --}}
        <nav class="flex-1 overflow-y-auto custom-scrollbar py-6 px-3 space-y-2">
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-black tracking-widest transition-all sidebar-link-hover
                      {{ $isHome ? 'module-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i class="ph-bold ph-squares-four text-lg"></i>
                <span x-show="sidebarOpen" x-transition>DASHBOARD</span>
            </a>

            <div class="pt-4 pb-2 px-4 text-[10px] font-black text-slate-600 tracking-[0.2em] uppercase" x-show="sidebarOpen">Modules</div>

            @foreach($navigation as $name => $module)
                @php $isActive = ($activeModuleName === $name); @endphp
                <a href="{{ route($module['main_route']) }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-black tracking-widest transition-all sidebar-link-hover
                          {{ $isActive ? 'module-active shadow-lg shadow-indigo-950/50' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <i class="ph-bold {{ $module['icon'] }} text-lg flex-shrink-0"></i>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">{{ $name }}</span>
                </a>
            @endforeach
        </nav>

        {{-- User Profile Footer --}}
        <div class="p-4 bg-black/20 border-t border-white/5">
            <div class="flex items-center gap-3 p-2">
                <div class="w-10 h-10 rounded-lg bg-indigo-500 flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div x-show="sidebarOpen" x-transition class="overflow-hidden">
                    <p class="text-xs font-bold truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[9px] text-slate-500 font-bold truncate uppercase tracking-tighter">{{ auth()->user()->roles->first()->name ?? 'User' }}</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- ─── CONTENT AREA ───────────────────────────────────────────────────── --}}
    <main class="flex-1 flex flex-col min-h-screen transition-all duration-300"
          :class="sidebarOpen ? 'ml-64' : 'ml-20'">
        
        {{-- Contextual Topbar --}}
        <header class="sticky top-0 z-40 bg-white border-b border-slate-200">
            {{-- Top Row: Info & Global Actions --}}
            <div class="h-14 px-8 flex items-center justify-between border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">{{ $activeModuleName }}</span>
                    <i class="ph ph-caret-right text-[10px] text-slate-300"></i>
                    <h2 class="text-sm font-bold text-slate-900">{{ $title ?? 'Overview' }}</h2>
                </div>
                <div class="flex items-center gap-4">
                    {{-- Global Search Component --}}
                    <div class="relative" x-data="{ 
                        searchQuery: '', 
                        results: [], 
                        showResults: false,
                        loading: false,
                        fetchResults() {
                            if (this.searchQuery.length < 2) {
                                this.results = [];
                                this.showResults = false;
                                return;
                            }
                            this.loading = true;
                            fetch(`/search?q=${encodeURIComponent(this.searchQuery)}`)
                                .then(res => res.json())
                                .then(data => {
                                    this.results = data;
                                    this.showResults = true;
                                    this.loading = false;
                                });
                        }
                    }">
                        <div class="relative group">
                            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition"></i>
                            <input type="text" 
                                   x-model="searchQuery" 
                                   @input.debounce.300ms="fetchResults()"
                                   @focus="if(results.length > 0) showResults = true"
                                   @click.away="showResults = false"
                                   @keydown.escape="showResults = false"
                                   placeholder="Search anything... (Ctrl + K)" 
                                   class="w-64 pl-10 pr-4 py-1.5 bg-slate-100 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-600/20 focus:bg-white transition-all duration-200">
                        </div>

                        {{-- Search Results Dropdown --}}
                        <div x-show="showResults" x-cloak
                             class="absolute left-0 mt-2 w-[400px] bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 overflow-hidden">
                            <div class="p-3 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Search Results</span>
                                <template x-if="loading">
                                    <i class="ph ph-circle-notch animate-spin text-indigo-600"></i>
                                </template>
                            </div>
                            <div class="max-h-[400px] overflow-y-auto custom-scrollbar py-2">
                                <template x-if="results.length === 0">
                                    <div class="px-4 py-8 text-center">
                                        <i class="ph ph-ghost text-3xl text-slate-200 mb-2"></i>
                                        <p class="text-xs font-bold text-slate-400">No results found for "<span x-text="searchQuery"></span>"</p>
                                    </div>
                                </template>
                                <template x-for="result in results" :key="result.url">
                                    <a :href="result.url" class="flex items-center gap-4 px-4 py-3 hover:bg-slate-50 transition group">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-indigo-600 group-hover:text-white transition">
                                            <i :class="'ph ph-bold ' + result.icon" class="text-lg"></i>
                                        </div>
                                        <div class="flex-1 overflow-hidden">
                                            <div class="flex items-center justify-between">
                                                <p class="text-xs font-bold text-slate-900 truncate" x-text="result.title"></p>
                                                <span class="text-[8px] font-black px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded uppercase tracking-tighter" x-text="result.type"></span>
                                            </div>
                                            <p class="text-[10px] text-slate-400 truncate mt-0.5" x-text="result.subtitle"></p>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Action Button --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                            <i class="ph ph-plus text-lg"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 overflow-hidden py-2">
                            <div class="px-4 py-2 border-b border-slate-50 mb-1">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Quick Create</span>
                            </div>
                            <a href="{{ route('employees.create') }}" class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                                <i class="ph ph-user-plus text-lg text-indigo-500"></i> New Employee
                            </a>
                            <a href="{{ route('projects.index') }}" class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                                <i class="ph ph-projector-screen text-lg text-emerald-500"></i> New Project
                            </a>
                            <a href="#" class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                                <i class="ph ph-check-square text-lg text-amber-500"></i> New Task
                            </a>
                            <a href="{{ route('sales.index') }}" class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                                <i class="ph ph-shopping-cart text-lg text-rose-500"></i> Sales Order
                            </a>
                        </div>
                    </div>

                    <button class="text-slate-400 hover:text-slate-600 transition"><i class="ph ph-bell text-lg"></i></button>
                    <div class="w-px h-4 bg-slate-200"></div>
                    
                    {{-- Profile Dropdown --}}
                    <div class="relative" x-data="{ userOpen: false }">
                        <button @click="userOpen = !userOpen" @click.away="userOpen = false"
                                class="flex items-center gap-3 pl-2 p-1 rounded-xl hover:bg-slate-100 transition duration-200">
                            <div class="text-right hidden sm:block">
                                <p class="text-xs font-bold text-slate-900 leading-none">{{ auth()->user()->name }}</p>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">{{ auth()->user()->roles->first()->name ?? 'Manager' }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-bold shadow-sm group-hover:bg-indigo-600 group-hover:text-white transition">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <i class="ph ph-caret-down text-[10px] text-slate-400 transition-transform" :class="userOpen ? 'rotate-180' : ''"></i>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div x-show="userOpen" x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 overflow-hidden py-2">
                            
                            <div class="px-4 py-3 border-b border-slate-50 mb-1">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Signed in as</p>
                                <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->email }}</p>
                            </div>

                            <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition">
                                <i class="ph ph-user-circle text-lg"></i>
                                My Profile
                            </a>
                            <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition">
                                <i class="ph ph-sliders text-lg"></i>
                                Preferences
                            </a>
                            <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition">
                                <i class="ph ph-shield-check text-lg"></i>
                                Security
                            </a>

                            <div class="h-px bg-slate-50 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 transition">
                                    <i class="ph ph-sign-out text-lg"></i>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom Row: Module Submenu (Surprise Contextual Navigation) --}}
            @if(count($activeModuleChildren) > 0)
                <div class="h-12 px-8 flex items-center gap-8 overflow-x-auto custom-scrollbar bg-slate-50/50">
                    @foreach($activeModuleChildren as $child)
                        @php $childActive = request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])); @endphp
                        <a href="{{ route($child['route']) }}"
                           class="h-full flex items-center text-xs font-bold uppercase tracking-widest transition-all whitespace-nowrap border-b-2 
                                  {{ $childActive ? 'text-indigo-600 border-indigo-600' : 'text-slate-500 border-transparent hover:text-indigo-400' }}">
                            {{ $child['label'] }}
                        </a>
                    @endforeach
                </div>
            @endif
        </header>

        {{-- Page Body --}}
        <div class="p-8 flex-1">
            @if(isset($slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </div>
    </main>

</body>
</html>
