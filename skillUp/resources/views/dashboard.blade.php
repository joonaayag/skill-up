@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <x-heading level="h1" class="mb-10">{{ __('messages.dashboard.hi') }}, {{ auth()->user()->name }}</x-heading>
    @if ($errors->any())
        <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="text-black dark:text-white">- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-14 h-full mb-24 ">
        <x-card>
            <x-heading level="h3" class="mb-8">{{ __('messages.dashboard.highlight-projects') }}</x-heading>
            <div class="flex flex-col gap-3">
                @if ($combined->isNotEmpty())
                    @foreach($combined as $project)
                        <a href="{{ route('projects.show', $project->id) }}">
                            <div
                                class="flex items-center space-x-4 hover:bg-themeLightGray/20 cursor-pointer p-1 rounded-lg transition">
                                <div class="bg-blue-100 p-2 rounded-md">
                                    <x-icon name="project" class="w-8 h-auto" />
                                </div>
                                <div>
                                    <strong>{{ $project->title }}</strong>
                                    <p class=" text-xs text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">
                                        {{ $project->general_category }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <p class="mt-6 text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">
                        {{ __('messages.dashboard.no-projects') }}.
                    </p>
                @endif
            </div>
        </x-card>

        <div class="grid grid-rows-5 gap-8">
            <x-card class="row-span-2 h-full">
                <x-heading level="h3" class="mb-8">{{ __('messages.dashboard.your-projects') }}</x-heading>
                <div class="flex flex-col gap-3">
                    @if ($ownProjects->isNotEmpty())
                        @foreach($ownProjects as $project)
                            <a href="{{ route('projects.show', $project->id) }}">
                                <div
                                    class="flex items-center space-x-4 hover:bg-themeLightGray/20 cursor-pointer p-1 rounded-lg transition">
                                    <div class="bg-blue-100 p-2 rounded-md">
                                        <x-icon name="project" class="w-8 h-auto" />
                                    </div>
                                    <div>
                                        <strong>{{ $project->title }}</strong>
                                        <p class="text-xs text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">
                                            {{ $project->description }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <p class="mt-6 text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">
                            {{ __('messages.dashboard.no-own-projects') }}.
                        </p>
                    @endif
                </div>
            </x-card>
            <x-card class="row-span-3 h-full">
                <x-heading level="h3" class="mb-8">{{ __('messages.dashboard.last-offers') }}</x-heading>
                <div class="flex flex-col gap-3">
                    @if ($jobOffers->isNotEmpty())
                        @foreach($jobOffers as $offer)
                            <a href="{{ route('job.offers.show', $offer->id) }}">
                                <div
                                    class="flex items-center space-x-4 hover:bg-themeLightGray/20 cursor-pointer p-1 rounded-lg transition">
                                    <div class="bg-blue-100 p-2 rounded-md">
                                        <x-icon name="briefcase" class="w-8 h-auto dark:text-black" />
                                    </div>
                                    <div class="[&>p]:mt-1">
                                        <strong>{{ $offer->name }}</strong>
                                        <p class="text-xs text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">
                                            {{ $offer->created_at->diffForHumans() }} - {{ $offer->company->name }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <p class="mt-6 text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">
                            {{ __('messages.dashboard.no-offers') }}.
                        </p>
                    @endif
                </div>
            </x-card>
        </div>

        <div class="grid grid-rows-7 gap-8 -mt-20">
            <div
                class="row-span-3 h-full [&>div]:h-full [&>div]:bg-white [&>div]:border-2 [&>div]:border-themeLightGray [&>div]:rounded-lg dark:[&>div]:bg-themeBgDark">
                <div class="relative">
                    <img src="{{ auth()->user()->banner ? asset('storage/' . auth()->user()->banner) : asset('images/defaultBanner.jpg')  }}"
                        alt="Fondo" class="w-full h-30 rounded-t-md object-cover" id="bannerImage">
                    <div class="absolute top-20 left-1/6 transform -translate-x-1/2">
                        <img src="{{ auth()->user()->profile ? asset('storage/' . auth()->user()->profile) : asset('images/defaultProfile.png') }}"
                            alt="Perfil" id="profileImage"
                            class="h-18 w-18 rounded-full border-4 border-white object-cover shadow-lg">

                    </div>
                    <div class="px-3 mt-8">
                        <x-heading level="h3">{{ auth()->user()->name . ' ' . auth()->user()->last_name }}</x-heading>
                        @if (auth()->user()->role === 'Alumno')
                            <p>{{ __('messages.dashboard.student-of') }} {{ auth()->user()->detail->educational_center }}</p>
                        @else
                            <p>{{ auth()->user()->role }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <x-card class="row-span-4 h-full">
                <div id="dashboard-notification-list">
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
                            {{ __('messages.dashboard.no-notifications') }}
                        </p>
                    @endif
                </div>
            </x-card>


        </div>

    </div>


    <div x-data="chatbot()" class="fixed bottom-6 right-6 z-50">

        <button @click="toggle"
            class="bg-themeBlue text-white rounded-full p-4 shadow-lg hover:bg-blue-700 cursor-pointer transition">
            💬
        </button>


        <div x-cloak x-show="open" @click.outside="open = false" x-transition
            class="mt-2 w-72 dark:bg-themeBgDark bg-white border rounded-lg shadow-xl p-4">
            <x-heading level="h3" class="mb-8">{{ __('messages.chatbot.skillup-assistant') }}</x-heading>
            <div class="h-48 overflow-y-auto text-sm space-y-2 mb-2" id="chat-window">
                <template x-for="msg in messages" :key="msg . id">
                    <div :class="msg . from === 'bot' ? 'text-left' : 'text-right'">
                        <span :class="msg . from === 'bot' ? 'dark:text-white text-black' : 'text-blue-600'"
                            x-text="msg.text"></span>
                    </div>
                </template>
            </div>

            <form @submit.prevent="send">
                <input x-model="input" type="text" class="w-full border px-2 py-1 rounded text-sm"
                    placeholder="{{ __('messages.chatbot.text-some') }}">
            </form>
        </div>
    </div>
    <script>
        function chatbot() {
            return {
                open: false,
                input: '',
                messages: [
                    { id: 1, text: '{{ __('messages.chatbot.first-message') }}', from: 'bot' },
                    { id: 2, text: '{{ __('messages.chatbot.second-message')}}', from: 'bot' }
                ],

                toggle() {
                    this.open = !this.open;
                    this.$nextTick(() => {
                        document.getElementById('chat-window').scrollTop = 9999;
                    });
                },
                send() {
                    if (this.input.trim() === '') return;

                    const userMsg = { id: Date.now(), text: this.input, from: 'user' };
                    this.messages.push(userMsg);

                    let response = '{{ __('messages.chatbot.no-understand') }}';
                    const txt = this.input.toLowerCase();

                    if (
                        txt.includes('{{ __('messages.chatbot.text-project') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-projects') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-see-projects') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-where-projects') }}')
                    ) {
                        response = '{{ __('messages.chatbot.response-project') }}';
                    } else if (
                        txt.includes('{{ __('messages.chatbot.text-offer') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-offers') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-job') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-vacancies') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-look-job') }}')
                    ) {
                        response = '{{ __('messages.chatbot.response-see-job-offers') }}.';
                    } else if (
                        txt.includes('{{ __('messages.chatbot.text-apply') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-register') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-how-apply') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-interested') }}')
                    ) {
                        response = '{{ __('messages.chatbot.response-apply-job-offer') }}';
                    } else if (
                        txt.includes('{{ __('messages.chatbot.text-company') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-create-job-offer') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-im-company') }}')
                    ) {
                        response = '{{ __('messages.chatbot.response-create-job-offer') }}.';
                    } else if (
                        txt.includes('{{ __('messages.chatbot.text-cannot-access') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-problem-access') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-forgot-password') }}')
                    ) {
                        response = '{{ __('messages.chatbot.response-password') }}.';
                    } else if (
                        txt.includes('{{ __('messages.chatbot.text-how-work') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-what-is') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-what-its-for') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-what-can-do') }}')
                    ) {
                        response = '{{ __('messages.chatbot.response-what-can-do') }}.';
                    } else if (
                        txt.includes('{{ __('messages.chatbot.text-hi') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-hello') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-hey') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-how-are-you') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-greetings') }}')
                    ) {
                        response = '{{ __('messages.chatbot.response-welcome') }}';
                    } else if (
                        txt.includes('{{ __('messages.chatbot.text-thanks') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-thaks-much') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-appreciate') }}')
                    ) {
                        response = '{{ __('messages.chatbot.response-your-welcome') }}';
                    } else if (
                        txt.includes('{{ __('messages.chatbot.text-bye') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-goodbye') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-see-you-later') }}')
                    ) {
                        response = '{{ __('messages.chatbot.response-goodbye') }}';
                    } else if (
                        txt.includes('{{ __('messages.chatbot.text-here') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-listen') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-help-me') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-someone-answer') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-anyone') }}')
                    ) {
                        response = '{{ __('messages.chatbot.response-help') }}.';
                    } else if (
                        txt.includes('{{ __('messages.chatbot.text-dk') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-lost') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-dont-understand') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-guide') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-what-I-do') }}')
                    ) {
                        response = '{{ __('messages.chatbot.response-lost') }}.';
                    } else if (
                        txt.includes('{{ __('messages.chatbot.text-how-you') }}') ||
                        txt.includes('{{ __('messages.chatbot.text-how-doing') }}')

                    ) {
                        response = '{{ __('messages.chatbot.response-chat-question') }}.';
                    }


                    this.messages.push({ id: Date.now() + 1, text: response, from: 'bot' });
                    this.input = '';
                    this.$nextTick(() => {
                        document.getElementById('chat-window').scrollTop = 9999;
                    });
                }
            }
        }
    </script>

@endsection