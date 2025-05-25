<textarea 
    {{ $attributes->merge([
        'class' => 'w-full px-3 py-2 rounded border text-xs md:tex-sm lg:text-base border-themeLightGray resize-none'
    ]) }}
>{{ $slot }}</textarea>
