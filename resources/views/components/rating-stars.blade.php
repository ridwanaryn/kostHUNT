@props(['rating' => 0, 'count' => null])

@php
    $value = (float) $rating;
    $rounded = (int) round($value);
@endphp

<span class="inline-flex items-center gap-0.5" title="{{ number_format($value, 1) }} out of 5">
    @for ($i = 1; $i <= 5; $i++)
        <span class="{{ $i <= $rounded ? 'text-yellow-500' : 'text-neutral-300' }}">&#9733;</span>
    @endfor
    @if ($count !== null)
        <span class="ml-1 text-xs font-bold text-neutral-600">{{ number_format($value, 1) }} ({{ $count }})</span>
    @endif
</span>
