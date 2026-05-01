<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - @yield('title')</title>

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/css/auth-institution.css','resources/css/auth-student.css','resources/css/auth.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- flash messages -->
    @if(session('success'))
        <div data-success-message="{{ session('success') }}" class="hidden"></div>
    @endif
    
    @if(session('error'))
        <div data-error-message="{{ session('error') }}" class="hidden"></div>
    @endif

    <!-- navbar -->
    @include('components.navbar')

    <!-- main content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>