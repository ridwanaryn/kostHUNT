@if (session('status') || $errors->any())
    <div class="fixed bottom-6 right-6 z-50 flex w-full max-w-sm flex-col gap-2">
        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)" x-cloak
                 class="bg-mint border-[3px] border-black rounded-xl px-5 py-3 font-bold shadow-[4px_4px_0px_#000]">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-cloak
                 class="bg-coral text-white border-[3px] border-black rounded-xl px-5 py-3 font-bold shadow-[4px_4px_0px_#000]">
                {{ $errors->first() }}
            </div>
        @endif
    </div>
@endif
