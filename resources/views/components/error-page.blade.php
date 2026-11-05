@props(['code', 'title', 'message'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $code }} — {{ $title }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:500,600,700|plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css'])
    </head>
    <body class="font-sans bg-canvas text-onyx min-h-screen flex items-center justify-center p-6">
        <div class="max-w-lg w-full bg-white border-[4px] border-black rounded-3xl shadow-[8px_8px_0px_#000] p-8 text-center">
            <span class="inline-block bg-yellow border-2 border-black rounded-xl px-3 py-1 font-display text-2xl font-black uppercase shadow-[3px_3px_0px_#000]">
                {{ $code }}
            </span>
            <h1 class="font-display text-2xl sm:text-3xl font-black uppercase mt-4">{{ $title }}</h1>
            <p class="text-neutral-700 mt-3">{{ $message }}</p>

            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <a href="{{ url('/') }}"
                   class="bg-cyan border-2 border-black rounded-lg px-4 py-2 font-black uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 transition">
                    Go home
                </a>
                <a href="javascript:history.back()"
                   class="bg-white border-2 border-black rounded-lg px-4 py-2 font-black uppercase shadow-[3px_3px_0px_#000] hover:-translate-y-0.5 transition">
                    Go back
                </a>
            </div>
        </div>
    </body>
</html>
