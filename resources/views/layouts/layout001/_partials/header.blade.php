<header class="bg-white dark:bg-gray-800 shadow-md">
    <nav class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <!-- Logo -->
            <a href="/" class="text-xl font-bold text-gray-800 dark:text-white hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                urCost
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('sors.index') }}"
                   class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition-colors {{ request()->routeIs('sors.*') ? 'font-bold text-blue-500 dark:text-blue-400' : '' }}">
                    SORs
                </a>
                <a href="{{ route('items.index') }}"
                   class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition-colors {{ request()->routeIs('items.*') ? 'font-bold text-blue-500 dark:text-blue-400' : '' }}">
                    Items
                </a>
                <a href="/demo"
                   class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition-colors {{ request()->is('demo') ? 'font-bold text-blue-500 dark:text-blue-400' : '' }}">
                    Demo
                </a>

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
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center space-x-4">
                <!-- Theme Toggle Button (Mobile) -->
                <button @click="toggleTheme()"
                        type="button"
                        class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        :aria-label="theme === 'light' ? 'Switch to dark mode' : 'Switch to light mode'">
                    <!-- Sun Icon (Light Mode) -->
                    <svg x-show="theme === 'light'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <!-- Moon Icon (Dark Mode) -->
                    <svg x-show="theme === 'dark'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>

                <!-- Hamburger Menu -->
                <button @click="sidebarOpen = !sidebarOpen"
                        type="button"
                        class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             @click.away="sidebarOpen = false"
             class="md:hidden pb-4">
            <div class="flex flex-col space-y-2">
                <a href="{{ route('sors.index') }}"
                   class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors {{ request()->routeIs('sors.*') ? 'bg-blue-50 dark:bg-blue-900 text-blue-500 dark:text-blue-400 font-bold' : '' }}">
                    SORs
                </a>
                <a href="{{ route('items.index') }}"
                   class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors {{ request()->routeIs('items.*') ? 'bg-blue-50 dark:bg-blue-900 text-blue-500 dark:text-blue-400 font-bold' : '' }}">
                    Items
                </a>
                <a href="/demo"
                   class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors {{ request()->is('demo') ? 'bg-blue-50 dark:bg-blue-900 text-blue-500 dark:text-blue-400 font-bold' : '' }}">
                    Demo
                </a>
            </div>
        </div>
    </nav>
</header>
