@if($notifications->count())
    <x-heading level="h3" class="mb-8">{{ __('messages.dashboard.recent-notifications') }}</x-heading>
    @foreach($notifications as $notification)
        <div
            class="flex items-center space-x-4 leading-card mb-2.5 hover:bg-themeLightGray/20 cursor-pointer p-1 rounded-lg transition">
            <div class="bg-themeGrape text-white p-2 rounded-full">
                <x-icon name="bell" class="w-8 h-auto" />
            </div>
            <div class="[&>p]:mt-1">
                <p class="font-semibold">{{ $notification->message }}</p>
                <p class="text-xs text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">
                    {{ $notification->created_at->diffForHumans() }}
                </p>
            </div>
        </div>
    @endforeach
@else
    <p class="mt-6 text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">
        {{ __('messages.dashboard.no-notifications') }}.</p>
@endif
