<header class="bg-white dark:bg-gray-800 shadow-md">
    <nav x-data="{ open: false }" class="container mx-auto px-4">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo -->
            <a href="{{ url('/') }}" class="text-xl font-bold text-gray-800 dark:text-white hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                {{ config('app.name', 'Laravel') }}
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('dashboard') }}"
                   class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'font-bold text-blue-500 dark:text-blue-400' : '' }}">
                    {{ __('Dashboard') }}
                </a>
                <a href="{{ route('sorCards') }}"
                   class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition-colors {{ request()->routeIs('sorCards') ? 'font-bold text-blue-500 dark:text-blue-400' : '' }}">
                    SOR List
                </a>

                {{-- Reports Dropdown --}}
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div>Reports</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('rate-cards.labor-report')">
                                {{ __('Labor Resource Rates') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('rate-cards.machine-report')">
                                {{ __('Machine Resource Rates') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>

                @Auth
                @if(auth()->user()->can('sor-admin'))
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div>SOR Admin</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @can('sor-admin')
                                <x-dropdown-link :href="route('admin.rate-calculation.index')">
                                    {{ __('Rate Calculation') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('sor.export.index')">
                                    {{ __('SOR Reports') }}
                                </x-dropdown-link>
                            @endcan
                            <x-dropdown-link :href="url('/demo')">
                                {{ __('demo') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/glass-demo')">
                                {{ __('glass-demo') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>
                @endif
                @endAuth

                @Auth
                @if(auth()->user()->can('user-list') || auth()->user()->can('role-list') || auth()->user()->can('permission-list'))
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div>User Management</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @can('user-list')
                                <x-dropdown-link :href="route('users.index')">
                                    {{ __('Users') }}
                                </x-dropdown-link>
                            @endcan
                            @can('role-list')
                                <x-dropdown-link :href="route('roles.index')">
                                    {{ __('Roles') }}
                                </x-dropdown-link>
                            @endcan
                            @can('permission-list')
                                <x-dropdown-link :href="route('permissions.index')">
                                    {{ __('Permissions') }}
                                </x-dropdown-link>
                            @endcan
                        </x-slot>
                    </x-dropdown>
                </div>
                @endif
                @endAuth

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

                <!-- Settings Dropdown -->
                @Auth
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
                @endAuth
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
                <button @click="open = ! open"
                        type="button"
                        class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div :class="{'block': open, 'hidden': ! open}" class="md:hidden pb-4">
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <a href="{{ route('sors.index') }}"
                   class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors {{ request()->routeIs('sors.index') ? 'bg-blue-50 dark:bg-blue-900 text-blue-500 dark:text-blue-400 font-bold' : '' }}">
                    SORs
                </a>
                
                {{-- Reports Section --}}
                <div class="pt-2 pb-1">
                    <div class="px-4 py-2">
                        <div class="font-medium text-sm text-gray-500 dark:text-gray-400">Reports</div>
                    </div>
                    <a href="{{ route('rate-cards.labor-report') }}"
                       class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors {{ request()->routeIs('rate-cards.labor-report') ? 'bg-blue-50 dark:bg-blue-900 text-blue-500 dark:text-blue-400 font-bold' : '' }}">
                        Labor Resource Rates
                    </a>
                    <a href="{{ route('rate-cards.machine-report') }}"
                       class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors {{ request()->routeIs('rate-cards.machine-report') ? 'bg-blue-50 dark:bg-blue-900 text-blue-500 dark:text-blue-400 font-bold' : '' }}">
                        Machine Resource Rates
                    </a>
                </div>
                
                @can('sor-admin')
                <a href="#"
                   class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors {{ request()->routeIs('sors.admin') ? 'bg-blue-50 dark:bg-blue-900 text-blue-500 dark:text-blue-400 font-bold' : '' }}">
                    SOR Admin
                </a>
                @endcan
                <a href="{{ url('/demo') }}"
                   class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors {{ request()->is('demo') ? 'bg-blue-50 dark:bg-blue-900 text-blue-500 dark:text-blue-400 font-bold' : '' }}">
                    Demo
                </a>
                @Auth
                @if(auth()->user()->can('user-list') || auth()->user()->can('role-list') || auth()->user()->can('permission-list'))
                <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800 dark:text-gray-200">User Management</div>
                    </div>
                    <div class="mt-3 space-y-1">
                        @can('user-list')
                            <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                {{ __('Users') }}
                            </x-responsive-nav-link>
                        @endcan
                        @can('role-list')
                            <x-responsive-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.*')">
                                {{ __('Roles') }}
                            </x-responsive-nav-link>
                        @endcan
                        @can('permission-list')
                            <x-responsive-nav-link :href="route('permissions.index')" :active="request()->routeIs('permissions.*')">
                                {{ __('Permissions') }}
                            </x-responsive-nav-link>
                        @endcan
                    </div>
                </div>
                @endif
                @endAuth
            </div>

            <!-- Responsive Settings Options -->
            @Auth
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
            @endAuth
        </div>
    </nav>
</header>
