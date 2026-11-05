<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('chat.index')" :active="request()->routeIs('chat.*')">
                        {{ __('Messages') }}
                    </x-nav-link>
                    @if (auth()->user()?->isTenant())
                        <x-nav-link :href="route('tenant.bookings.index')" :active="request()->routeIs('tenant.bookings.*')">
                            {{ __('My Bookings') }}
                        </x-nav-link>
                        <x-nav-link :href="route('tenant.invoices.index')" :active="request()->routeIs('tenant.invoices.*')">
                            {{ __('Invoices') }}
                        </x-nav-link>
                        <x-nav-link :href="route('tenant.favorites.index')" :active="request()->routeIs('tenant.favorites.*')">
                            {{ __('Favorites') }}
                        </x-nav-link>
                        <x-nav-link :href="route('tenant.maintenance-requests.index')" :active="request()->routeIs('tenant.maintenance-requests.*')">
                            {{ __('Requests') }}
                        </x-nav-link>
                    @endif
                    @if (auth()->user()?->isOwner())
                        <x-nav-link :href="route('owner.buildings.index')" :active="request()->routeIs('owner.buildings.*') || request()->routeIs('owner.rooms.*')">
                            {{ __('My Buildings') }}
                        </x-nav-link>
                        <x-nav-link :href="route('owner.bookings.index')" :active="request()->routeIs('owner.bookings.*')">
                            {{ __('Bookings') }}
                        </x-nav-link>
                        <x-nav-link :href="route('owner.invoices.index')" :active="request()->routeIs('owner.invoices.*')">
                            {{ __('Invoices') }}
                        </x-nav-link>
                        <x-nav-link :href="route('owner.maintenance-requests.index')" :active="request()->routeIs('owner.maintenance-requests.*')">
                            {{ __('Maintenance') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Notifications Bell -->
            <div class="hidden sm:flex sm:items-center">
                <a href="{{ route('notifications.index') }}" class="relative inline-flex items-center justify-center p-2 text-gray-500 hover:text-gray-700" aria-label="Notifications">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 11-6 0" />
                    </svg>
                    @if (($unreadNotificationsCount ?? 0) > 0)
                        <span class="absolute top-0 right-0 bg-coral text-white border border-black rounded-full text-[10px] font-black px-1 leading-4">
                            {{ $unreadNotificationsCount }}
                        </span>
                    @endif
                </a>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
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

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('chat.index')" :active="request()->routeIs('chat.*')">
                {{ __('Messages') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">
                {{ __('Notifications') }} @if (($unreadNotificationsCount ?? 0) > 0) ({{ $unreadNotificationsCount }}) @endif
            </x-responsive-nav-link>
            @if (auth()->user()?->isTenant())
                <x-responsive-nav-link :href="route('tenant.bookings.index')" :active="request()->routeIs('tenant.bookings.*')">
                    {{ __('My Bookings') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tenant.invoices.index')" :active="request()->routeIs('tenant.invoices.*')">
                    {{ __('Invoices') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tenant.favorites.index')" :active="request()->routeIs('tenant.favorites.*')">
                    {{ __('Favorites') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tenant.maintenance-requests.index')" :active="request()->routeIs('tenant.maintenance-requests.*')">
                    {{ __('Requests') }}
                </x-responsive-nav-link>
            @endif
            @if (auth()->user()?->isOwner())
                <x-responsive-nav-link :href="route('owner.buildings.index')" :active="request()->routeIs('owner.buildings.*') || request()->routeIs('owner.rooms.*')">
                    {{ __('My Buildings') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('owner.bookings.index')" :active="request()->routeIs('owner.bookings.*')">
                    {{ __('Bookings') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('owner.invoices.index')" :active="request()->routeIs('owner.invoices.*')">
                    {{ __('Invoices') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('owner.maintenance-requests.index')" :active="request()->routeIs('owner.maintenance-requests.*')">
                    {{ __('Maintenance') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
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
    </div>
</nav>
