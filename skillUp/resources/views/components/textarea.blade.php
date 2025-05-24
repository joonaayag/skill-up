<textarea 
    {{ $attributes->merge([
        'class' => 'w-full px-3 py-2 rounded border border-themeLightGray resize-none'
    ]) }}
>{{ $slot }}</textarea>
