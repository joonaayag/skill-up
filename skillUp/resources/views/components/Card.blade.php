@props(['class' => ''])

<div
    class="[&>div]:h-full [&>div]:bg-white [&>div]:border-2 [&>div]:border-themeLightGray [&>div]:rounded-lg dark:[&>div]:bg-themeBgDark dark:[&>div]:border-themeBlue {{ $class }}">
    <div class="py-3 px-4">
        {{ $slot }}
    </div>
</div>