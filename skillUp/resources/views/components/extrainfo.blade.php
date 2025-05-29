<div 
    x-data="{ showTooltip: false }"
    @mouseenter="showTooltip = true" 
    @mouseleave="showTooltip = false" 
    class="relative cursor-pointer"
>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <title>Info</title>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
    </svg>

    <div 
        x-show="showTooltip" 
        x-transition 
        class="absolute z-10 w-48 text-xs text-white bg-black rounded px-2 py-1 -top-10 left-20 transform -translate-x-1/2 whitespace-normal"
    >
        {{ $text }}
    </div>
</div>
