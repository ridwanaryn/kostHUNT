{{-- Expects: $photos (collection), $uploadRoute --}}
<section class="bg-white border-2 border-black rounded-2xl shadow-[4px_4px_0px_#000] p-6">
    <h3 class="font-display text-lg font-bold uppercase mb-4">Photos</h3>

    @if ($photos->isEmpty())
        <p class="text-sm text-gray-600 mb-4">No photos yet. The first uploaded photo becomes the cover.</p>
    @else
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            @foreach ($photos as $photo)
                <div class="relative border-2 border-black rounded-xl overflow-hidden bg-canvas">
                    <img src="{{ $photo->url() }}" alt="Photo" class="w-full h-28 object-cover">
                    @if ($photo->is_cover)
                        <span class="absolute top-1 left-1 bg-mint border-2 border-black rounded-full px-2 py-0.5 text-[10px] font-bold uppercase">Cover</span>
                    @endif
                    <div class="flex items-center gap-2 p-2 bg-white border-t-2 border-black">
                        @unless ($photo->is_cover)
                            <form method="POST" action="{{ route('owner.photos.cover', $photo) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-xs font-bold underline">Set cover</button>
                            </form>
                        @endunless
                        <form method="POST" action="{{ route('owner.photos.destroy', $photo) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-bold text-coral underline">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3">
        @csrf
        <div class="flex-1 min-w-[200px]">
            <label for="photos" class="block font-bold text-sm uppercase tracking-wide mb-1">Add photos</label>
            <input id="photos" name="photos[]" type="file" multiple accept="image/*"
                   class="w-full border-2 border-black rounded-lg px-3 py-2 bg-white file:mr-3 file:border-2 file:border-black file:rounded-md file:px-3 file:py-1 file:font-bold file:bg-yellow">
        </div>
        <button type="submit"
                class="bg-yellow border-2 border-black rounded-lg px-4 py-2 font-bold uppercase shadow-[3px_3px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0 active:translate-y-0 active:shadow-none transition">
            Upload
        </button>
        @error('photos') <p class="w-full text-sm font-bold text-coral">{{ $message }}</p> @enderror
        @error('photos.*') <p class="w-full text-sm font-bold text-coral">{{ $message }}</p> @enderror
    </form>
</section>
