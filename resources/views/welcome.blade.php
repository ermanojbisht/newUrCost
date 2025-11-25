<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'urCost') }} - Cost Management Simplified</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200"
          x-data="{
              theme: localStorage.getItem('theme') || 'dark',
              toggleTheme() {
                  this.theme = this.theme === 'light' ? 'dark' : 'light';
                  localStorage.setItem('theme', this.theme);
                  this.updateThemeClass();
              },
              updateThemeClass() {
                  if (this.theme === 'dark') {
                      document.documentElement.classList.add('dark');
                      document.documentElement.classList.remove('light');
                  } else {
                      document.documentElement.classList.add('light');
                      document.documentElement.classList.remove('dark');
                  }
              }
          }"
          x-init="
              updateThemeClass();
              $watch('theme', () => updateThemeClass());
          ">
        <div class="min-h-screen flex flex-col items-center justify-center p-4">
            <header class="w-full max-w-4xl flex justify-between items-center py-4">
                <div class="text-2xl font-bold text-gray-800 dark:text-white">
                    urCost
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Theme Toggle Button -->
                    <button @click="toggleTheme()"
                            type="button"
                            class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            :aria-label="theme === 'light' ? 'Switch to dark mode' : 'Switch to light mode'">
                        <!-- Sun Icon (Light Mode) -->
                        <svg x-show="theme === 'light'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 rotate-180"
                             x-transition:enter-end="opacity-100 rotate-0"
                             class="w-6 h-6"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>

                        <!-- Moon Icon (Dark Mode) -->
                        <svg x-show="theme === 'dark'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 rotate-180"
                             x-transition:enter-end="opacity-100 rotate-0"
                             class="w-6 h-6"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                            </path>
                        </svg>
                    </button>

                    @if (Route::has('login'))
                        <nav class="flex items-center space-x-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn-primary">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="btn-secondary">Log in</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn-primary ml-2">Register</a>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </div>
            </header>

            <main class="flex-1 flex flex-col items-center justify-center w-full max-w-4xl text-center py-12">
                <!-- Hero Section -->
                <div class="card-glass p-8 mb-8 w-full"
                     x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-500 transform"
                     x-transition:enter-start="opacity-0 translate-y-10"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white leading-tight mb-4"
                        x-data="{ show: false }" x-init="setTimeout(() => show = true, 200)"
                        x-show="show"
                        x-transition:enter="transition ease-out duration-500 transform"
                        x-transition:enter-start="opacity-0 translate-y-10"
                        x-transition:enter-end="opacity-100 translate-y-0">
                        Streamline Your Cost Analysis with <span class="text-blue-600 dark:text-blue-400">UK SOR</span>
                    </h1>
                    <p class="text-lg md:text-xl text-gray-700 dark:text-gray-300 mb-8"
                       x-data="{ show: false }" x-init="setTimeout(() => show = true, 300)"
                       x-show="show"
                       x-transition:enter="transition ease-out duration-500 transform"
                       x-transition:enter-start="opacity-0 translate-y-10"
                       x-transition:enter-end="opacity-100 translate-y-0">
                        Efficiently manage resources, rates, and projects with our intuitive platform.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4"
                         x-data="{ show: false }" x-init="setTimeout(() => show = true, 400)"
                         x-show="show"
                         x-transition:enter="transition ease-out duration-500 transform"
                         x-transition:enter-start="opacity-0 translate-y-10"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <a href="{{ route('sorCards') }}" class="btn-primary btn-lg transition-all duration-300 hover:scale-105">View SOR</a>
                        <a href="#features" class="btn-secondary btn-lg transition-all duration-300 hover:scale-105">Learn More</a>
                    </div>
                </div>

                <!-- Features Section -->
                <section id="features" class="w-full py-12">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8"
                        x-data="{ show: false }" x-init="setTimeout(() => show = true, 500)"
                        x-show="show"
                        x-transition:enter="transition ease-out duration-500 transform"
                        x-transition:enter-start="opacity-0 translate-y-10"
                        x-transition:enter-end="opacity-100 translate-y-0">
                        Key Features
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="card-glass p-6 flex flex-col items-center text-center transition-all duration-300 hover:scale-105 hover:shadow-xl"
                             x-data="{ show: false }" x-init="setTimeout(() => show = true, 600)"
                             x-show="show"
                             x-transition:enter="transition ease-out duration-500 transform"
                             x-transition:enter-start="opacity-0 translate-y-10"
                             x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="text-blue-500 mb-4">
                                {!! config('icons.chart') !!}
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Accurate Rate Management</h3>
                            <p class="text-gray-700 dark:text-gray-300">Manage and analyze rates with precision for better project planning.</p>
                        </div>
                        <div class="card-glass p-6 flex flex-col items-center text-center transition-all duration-300 hover:scale-105 hover:shadow-xl"
                             x-data="{ show: false }" x-init="setTimeout(() => show = true, 700)"
                             x-show="show"
                             x-transition:enter="transition ease-out duration-500 transform"
                             x-transition:enter-start="opacity-0 translate-y-10"
                             x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="text-green-500 mb-4">
                                {!! config('icons.calculator') !!}
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Project Cost Estimation</h3>
                            <p class="text-gray-700 dark:text-gray-300">Get reliable cost estimates for your projects, every time.</p>
                        </div>
                        <div class="card-glass p-6 flex flex-col items-center text-center transition-all duration-300 hover:scale-105 hover:shadow-xl"
                             x-data="{ show: false }" x-init="setTimeout(() => show = true, 800)"
                             x-show="show"
                             x-transition:enter="transition ease-out duration-500 transform"
                             x-transition:enter-start="opacity-0 translate-y-10"
                             x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="text-purple-500 mb-4">
                                {!! config('icons.optimize') !!}
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Resource Optimization</h3>
                            <p class="text-gray-700 dark:text-gray-300">Optimize resource allocation to maximize efficiency and reduce waste.</p>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="w-full max-w-4xl text-center py-4 text-gray-600 dark:text-gray-400 text-sm mt-8">
                &copy; {{ date('Y') }} urCost. All rights reserved.
            </footer>
        </div>
    </body>
</html>
