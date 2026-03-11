<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Leave Management') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space+grotesk:400,500,600,700&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=source+sans+3:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-base-900 antialiased">
        <div class="min-h-screen erp-backdrop flex flex-col items-center justify-center px-4 py-12">
            <div class="mb-8 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-600 text-white shadow-soft">
                    <span class="text-xl font-bold">LM</span>
                </div>
                <h1 class="mt-4 text-2xl font-semibold">LeaveSphere</h1>
                <p class="mt-2 text-sm text-base-500">Enterprise-grade time off management</p>
            </div>

            <div class="w-full max-w-md rounded-2xl border border-base-100/70 bg-base-0/85 p-6 shadow-glass backdrop-blur dark:border-base-200/40 dark:bg-base-0/10">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
