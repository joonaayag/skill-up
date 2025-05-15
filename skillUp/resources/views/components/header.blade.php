<header
    class="fixed top-0 left-1/2 -translate-x-1/2 {{ auth()->user()?->role === 'Profesor' ? 'w-[95%]' : 'w-[90%]' }} flex items-center justify-between bg-red
  dark:bg-themeBgDark bg-white text-black dark:text-[#e8e8e8] px-8 py-2 rounded-b-4xl shadow z-50 dark:border-2 dark:border-t-0 dark:border-themeBlue"
    id="main-header">

    <div class="flex flex-grow basis-0">
        <a href="{{ route('dashboard') }}">
            <x-icon name="skill-up-logo" class="w-48 h-auto fill-current -translate-x-5" />
        </a>
    </div>

    <nav class="flex [&>a]:inline-block [&>a]:px-3 [&>a]:py-2" id="main-navigation">
        <a href="{{ route('dashboard') }}">{{ __('messages.navbar.home') }}</a>
        <a href="{{ route('projects.index') }}">{{ __('messages.navbar.projects') }}</a>
        <a href="{{ route('job.offers.index') }}">{{ __('messages.navbar.job-offers') }}</a>
        <a href="{{ route('favorites.index') }}">{{ __('messages.navbar.favorites') }}</a>

        @auth
            @php
                $role = auth()->user()->role;
            @endphp

            @if(in_array($role, ['Usuario', 'Alumno']))
                <a href="{{ route('projects.ownProjects') }}">Tus proyectos</a>
            @endif
            @if($role === 'Profesor')
                <a href="{{ route('school.projects.index') }}">{{ __('messages.navbar.school-projects') }}</a>
            @endif
            @if(in_array($role, ['Profesor', 'Empresa']))
                <a href="{{ route('applications.index') }}">{{ __('messages.navbar.applications') }}</a>
                <a href="{{ route('job.offers.company.index') }}">{{ __('messages.navbar.my-job-offers') }}</a>
            @endif
            @if($role === 'Admin')
                <a href="{{ route('admin.dashboard') }}">{{ __('messages.navbar.admin-panel') }}</a>
            @endif
        @endauth

        <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false"
            class="relative inline-block">
            <button class="relative px-3 py-2 cursor-pointer">
                <x-icon name="bell" class="w-6 h-auto fill-none" />
                @if(auth()->user()->notifications->count() > 0)
                    <span class="absolute -top-0 right-1 bg-red-500 text-white text-xs rounded-full px-1">
                        {{ auth()->user()->notifications->count() }}
                    </span>
                @endif
            </button>

            <div x-cloak x-show="open" x-transition
                class="absolute left-0 mt-2 w-72 dark:bg-themeBgDark bg-white border border-gray-300 shadow-xl rounded-md z-50">
                <ul class="max-h-64 overflow-y-auto">
                    @forelse(auth()->user()->notifications as $notification)
                        <li
                            class="flex flex-row justify-between items-start p-3 hover:bg-themeLightGray/20 [&>div>span]text-themeLightGray cursor-default transition">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold">{{ $notification->title }}</span>
                                <span class="text-sm">{{ $notification->message}}</span>
                            </div>
                            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"><x-icon name="x"
                                        class="w-6 h-auto text-themeRed cursor-pointer" /></button>
                            </form>
                        </li>
                    @empty
                        <li class="p-3 text-sm text-gray-500">{{ __('messages.navbar.no-notifications') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <button id="theme-toggle" @click="darkMode = !darkMode"
            class="rounded-full ml-4 cursor-pointer hover:transform hover:rotate-180 hover:transition-all">
            <x-icon name="dark-light" class="w-6 h-auto " />
        </button>


        @php
            $languages = [
                'en' => ['label' => 'English', 'icon' => asset('icons/england.svg')],
                'es' => ['label' => 'Español', 'icon' => asset('icons/spain.svg')],
                'fr' => ['label' => 'Français', 'icon' => asset('icons/french.svg')],
            ];
        @endphp

        <div x-data="{ open: false }" class="relative inline-block translate-y-1.5 ml-2 text-left z-50">
            <button @click="open = !open"
                class="flex items-center gap-2 px-3 py-1 rounded dark:bg-themeDark dark:text-white cursor-pointer">
                <img src="{{ $languages[App::getLocale()]['icon'] }}" alt="Bandera" class="w-5 h-5">
                <span class="text-sm">{{ $languages[App::getLocale()]['label'] }}</span>
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" @click.outside="open = false"
                class="absolute right-0 mt-2 w-40 dark:bg-themeBgDark bg-white dark:bg-themeDark border border-gray-200 dark:border-gray-600 rounded shadow">
                @foreach ($languages as $locale => $info)
                    <a href="{{ LaravelLocalization::getLocalizedURL($locale, null, [], true) }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700
                        {{ App::getLocale() === $locale ? 'font-semibold' : '' }}">
                        <img src="{{ $info['icon'] }}" alt="{{ $info['label'] }}" class="w-5 h-5">
                        {{ $info['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

    </nav>

    <nav class="flex flex-grow justify-end basis-0 ">
        @auth
            <a href="{{ route('profile.index') }}" class="flex flex-row items-center space-x-2 px-3 py-2 hover:bg-black/5 dark:hover:bg-white/9 
                        transition border-b-2 border-transparent rounded hover:border-b-2 hover:border-b-themeBlue">
                <span>{{ auth()->user()->name }}</span>
                <img src="{{ auth()->user()->profile ? asset('storage/' . auth()->user()->profile) : 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/Windows_10_Default_Profile_Picture.svg/2048px-Windows_10_Default_Profile_Picture.svg.png' }}"
                    alt="Perfil" id="profileImage"
                    class="size-8 rounded-full border-2 border-themeBlue dark:border-white object-cover shadow-lg">
            </a>
        @endauth
    </nav>

    <div id="menu-backdrop" class="absolute bg-black/5 dark:bg-white/9 border-b-2 border-b-themeBlue backdrop-blur-lg rounded translate-x-[var(--left)] translate-y-[var(--top)] left-0 top-0 w-[var(--width)]
         h-[var(--height)] transition-all duration-200 ease-in-out opacity-0 -z-10">
    </div>

</header>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const listItem = document.querySelectorAll('#main-navigation a')
        const menuBackdrop = document.getElementById('menu-backdrop')
        const header = document.getElementById('main-header')

        listItem.forEach((item) => {
            item.addEventListener('mouseenter', (e) => {
                const headerRect = header.getBoundingClientRect()
                const { left, top, width, height } = e.target.getBoundingClientRect()

                const relativeLeft = left - headerRect.left
                const relativeTop = top - headerRect.top

                menuBackdrop.style.setProperty('--left', `${relativeLeft}px`)
                menuBackdrop.style.setProperty('--top', `${relativeTop}px`)
                menuBackdrop.style.setProperty('--width', `${width}px`)
                menuBackdrop.style.setProperty('--height', `${height}px`)

                menuBackdrop.style.opacity = '1'
                menuBackdrop.style.visibility = 'visible'
            })

            item.addEventListener('mouseleave', () => {
                menuBackdrop.style.opacity = '0'
                menuBackdrop.style.visibility = 'hidden'
            })
        })

        window.addEventListener('resize', function () {
            menuBackdrop.style.opacity = '0'
            menuBackdrop.style.visibility = 'hidden'
        })
    })
</script>