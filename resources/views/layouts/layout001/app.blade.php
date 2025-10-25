<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" :class="{ 'dark': isDark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>urCost Conversion</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-sans antialiased" x-data="{
    isDark: true,
    toggleTheme() {
        this.isDark = !this.isDark;
    }
}">

    <div class="min-h-screen">
        @include('layouts.layout001._partials.header')

        <main class="container mx-auto px-4 py-8">
            @yield('content')
        </main>

        @include('layouts.layout001._partials.footer')
    </div>

</body>
</html>