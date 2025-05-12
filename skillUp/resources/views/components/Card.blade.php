@props(['class' => ''])

<div class="bg-white dark:bg-themeBgDark border-2 border-themeLightGray rounded-lg py-3 px-4 {{ $class }}">
    {{ $slot }}
</div>
