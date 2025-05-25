<div 
    x-show="{{ $show ?? 'showModal' }}"
    x-transition.opacity
    class="fixed inset-0 bg-black/50 backdrop-blur-sm px-4 z-40 overflow-y-auto"
    @click.self="{{ $show ?? 'showModal' }} = false"
>
    <div
        x-show="{{ $show ?? 'showModal' }}"
        x-transition
        class="bg-white dark:bg-themeBgDark rounded-xl shadow-xl max-w-2xl w-full mx-auto mt-32 lg:mt-24 mb-10 p-6"
    >
        {{ $slot }}
    </div>
</div>
