@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? $title.' — '.config('app.name') : config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:500,600,700|plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans bg-canvas text-onyx antialiased">
        <div class="bg-yellow border-b-[3px] border-black overflow-hidden">
            <div class="flex w-max animate-marquee py-2 text-[11px] font-bold uppercase tracking-widest">
                @for ($i = 0; $i < 2; $i++)
                    <span class="mx-6">360&deg; online survey</span>
                    <span class="mx-2">&bull;</span>
                    <span class="mx-6">Student cashback</span>
                    <span class="mx-2">&bull;</span>
                    <span class="mx-6">No deposit required</span>
                    <span class="mx-2">&bull;</span>
                    <span class="mx-6">Verified rooms only</span>
                    <span class="mx-2">&bull;</span>
                @endfor
            </div>
        </div>

        <header x-data="{ open: false }" class="bg-canvas border-b-[3px] border-black sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">
                    <a href="{{ route('home') }}" class="shrink-0">
                        <span class="inline-block bg-yellow border-[3px] border-black rounded-xl px-3 py-1.5 font-display text-lg font-black uppercase shadow-[3px_3px_0px_#000]">kostHUNT!</span>
                    </a>

                    <nav class="hidden md:flex items-center gap-7 text-sm font-bold uppercase">
                        <a href="{{ route('rooms.index') }}" class="hover:underline underline-offset-4">Browse rooms</a>
                        <a href="{{ route('home') }}#why" class="hover:underline underline-offset-4">Why us</a>
                    </nav>

                    <div class="hidden md:flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}"
                               class="border-2 border-black rounded-xl px-4 py-2 text-sm font-bold uppercase bg-white shadow-[2px_2px_0px_#000] hover:-translate-y-0.5 transition">
                                Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="border-2 border-black rounded-xl px-4 py-2 text-sm font-bold uppercase bg-pink shadow-[2px_2px_0px_#000] hover:-translate-y-0.5 transition">
                                    Log out
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-bold uppercase hover:underline underline-offset-4">Log in</a>
                            <a href="{{ route('register') }}"
                               class="border-2 border-black rounded-xl px-4 py-2 text-sm font-bold uppercase bg-pink shadow-[2px_2px_0px_#000] hover:-translate-y-0.5 transition">
                                Register
                            </a>
                        @endauth
                    </div>

                    <button @click="open = ! open" class="md:hidden border-2 border-black rounded-lg p-2 bg-white" aria-label="Toggle menu">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <div x-show="open" x-cloak class="md:hidden border-t-[3px] border-black bg-white px-4 py-4 space-y-3">
                <a href="{{ route('rooms.index') }}" class="block font-bold uppercase">Browse rooms</a>
                <a href="{{ route('home') }}#why" class="block font-bold uppercase">Why us</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block font-bold uppercase">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block font-bold uppercase">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block font-bold uppercase">Log in</a>
                    <a href="{{ route('register') }}" class="block font-bold uppercase">Register</a>
                @endauth
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        <footer class="bg-white border-t-[4px] border-black mt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="bg-yellow border-[3px] border-black rounded-2xl shadow-[6px_6px_0px_#000] p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
                    <div>
                        <h3 class="font-display text-2xl font-black uppercase">Get student-only deals</h3>
                        <p class="text-sm font-medium text-neutral-800">Join the newsletter for new rooms and promos.</p>
                    </div>
                    <div class="flex w-full md:w-auto gap-2">
                        <input type="email" placeholder="you@campus.ac.id"
                               class="w-full md:w-64 border-[3px] border-black rounded-lg px-3 py-2 bg-white focus:outline-none">
                        <button type="button"
                                class="bg-cyan border-[3px] border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 transition">
                            Subscribe
                        </button>
                    </div>
                </div>

                <div class="mt-10 grid gap-8 md:grid-cols-4">
                    <div>
                        <span class="inline-block bg-yellow border-[3px] border-black rounded-xl px-3 py-1.5 font-display text-lg font-black uppercase shadow-[3px_3px_0px_#000]">kostHUNT!</span>
                        <p class="mt-3 text-sm text-neutral-700">Find and rent your next kost without the drama.</p>
                    </div>
                    <div>
                        <h4 class="font-display font-black uppercase mb-3">Explore</h4>
                        <ul class="space-y-2 text-sm font-medium">
                            <li><a href="{{ route('rooms.index') }}" class="hover:underline">All rooms</a></li>
                            <li><a href="{{ route('rooms.index', ['sort' => 'price_asc']) }}" class="hover:underline">Cheapest first</a></li>
                            <li><a href="{{ route('rooms.index', ['sort' => 'newest']) }}" class="hover:underline">Newest</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-display font-black uppercase mb-3">Popular cities</h4>
                        <ul class="space-y-2 text-sm font-medium">
                            @foreach (['Bandung', 'Jakarta', 'Yogyakarta', 'Surabaya'] as $city)
                                <li><a href="{{ route('rooms.index', ['city' => $city]) }}" class="hover:underline">{{ $city }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-display font-black uppercase mb-3">Follow</h4>
                        <div class="flex gap-2">
                            <span class="border-2 border-black rounded-full px-3 py-1 text-xs font-bold bg-canvas">Instagram</span>
                            <span class="border-2 border-black rounded-full px-3 py-1 text-xs font-bold bg-canvas">TikTok</span>
                        </div>
                    </div>
                </div>

                <p class="mt-10 text-xs font-bold text-neutral-600">&copy; {{ date('Y') }} kostHUNT! — Built as a Laravel portfolio project.</p>
            </div>
        </footer>

        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-cloak
                 class="fixed bottom-6 right-6 z-50 max-w-sm bg-mint border-[3px] border-black rounded-xl px-5 py-3 font-bold shadow-[4px_4px_0px_#000]">
                {{ session('status') }}
            </div>
        @endif
    </body>
</html>
