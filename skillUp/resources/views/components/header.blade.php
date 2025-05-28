<header
    class="fixed top-0 left-1/2 -translate-x-1/2 {{ auth()->user()?->role === 'Profesor' ? 'w-full 3xl:w-[95%]' : 'w-full 3xl:w-[90%]' }} flex items-center justify-between bg-red
  dark:bg-themeBgDark bg-white text-black dark:text-[#e8e8e8] px-8 py-2 rounded-b-4xl shadow z-50 dark:border-2 dark:border-t-0 dark:border-themeBlue"
    id="main-header"
    x-data="{ mobileMenuOpen: false }">

    <!-- Logo -->
    <div class="flex flex-grow xl:basis-0">
        <a href="{{ route('dashboard') }}">
            <x-icon name="skill-up-logo" class="w-48 h-auto fill-current -translate-x-5" />
        </a>
    </div>

    <!-- Desktop Navigation (XL and up) -->
    <nav class="hidden 2xl:flex [&>a]:inline-block [&>a]:px-3 [&>a]:py-2" id="main-navigation">
        <a href="{{ route('dashboard') }}">{{ __('messages.navbar.home') }}</a>
        <a href="{{ route('projects.index') }}">{{ __('messages.navbar.projects') }}</a>
        <a href="{{ route('job.offers.index') }}">{{ __('messages.navbar.job-offers') }}</a>
        <a href="{{ route('favorites.index') }}">{{ __('messages.navbar.favorites') }}</a>

        @auth
            @php
                $role = auth()->user()->role;
            @endphp

            @if(in_array($role, ['Usuario', 'Alumno']))
                <a href="{{ route('projects.ownProjects') }}">{{ __('messages.navbar.my-projects') }}</a>
            @endif
            @if($role === 'Profesor')
                <a href="{{ route('school.projects.index') }}">{{ __('messages.navbar.school-projects') }}</a>
                <a href="{{ route('admin.dashboard') }}">AP</a>
            @endif
            @if(in_array($role, ['Profesor', 'Empresa']))
                <a href="{{ route('applications.index') }}">{{ __('messages.navbar.applications') }}</a>
                <a href="{{ route('job.offers.company.index') }}">{{ __('messages.navbar.my-job-offers') }}</a>
            @endif
            @if($role === 'Admin')
                <a href="{{ route('admin.dashboard') }}">{{ __('messages.navbar.admin-panel') }}</a>
            @endif
        @endauth

        <!-- Notifications Dropdown (Desktop) -->
        <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false"
            class="relative inline-block">
            <button class="relative px-3 py-2 cursor-pointer">
                <x-icon name="bell" class="w-6 h-auto fill-none" />
                @if(auth()->user()->notifications->count() > 0)
                    <span id="notification-count"
                        class="absolute -top-0 right-1 bg-red-500 text-white text-xs rounded-full px-1">
                        {{ auth()->user()->notifications->count() }}
                    </span>
                @endif
            </button>

            <div x-cloak x-show="open" x-transition
                class="absolute left-0 mt-2 w-72 dark:bg-themeBgDark bg-white border border-gray-300 shadow-xl rounded-md z-50">
                <ul class="max-h-64 overflow-y-auto" data-notification-list>
                    @forelse(auth()->user()->notifications as $notification)
                        <li
                            class="flex flex-row justify-between items-start p-3 hover:bg-themeLightGray/20 [&>div>span]text-themeLightGray cursor-default transition">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold">{{ $notification->title }}</span>
                                <span class="text-sm">{{ $notification->message}}</span>
                            </div>
                            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}"
                                class="form-eliminar-notificacion" data-id="{{ $notification->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-eliminar">
                                    <x-icon name="cross" class="w-6 h-auto text-themeRed cursor-pointer" />
                                </button>
                            </form>
                        </li>
                    @empty
                        <li class="p-3 text-sm text-gray-500">{{ __('messages.navbar.no-notifications') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Theme Toggle (Desktop) -->
        <div x-data="{ rotating: false }">
            <button id="theme-toggle-mobile" 
                @click="darkMode = !darkMode; rotating = true; setTimeout(() => rotating = false, 600)"
                :class="{'rotate-[360deg]': rotating}"
                class="p-2 rounded-full cursor-pointer transition-transform duration-500 ease-in-out transform">
                <x-icon name="dark-light" class="w-6 h-auto" />
            </button>
        </div>

        <!-- Language Selector (Desktop) -->
        @php
            $languages = [
                'en' => ['label' => 'English', 'icon' => asset('icons/england.svg')],
                'es' => ['label' => 'Español', 'icon' => asset('icons/spain.svg')],
                'fr' => ['label' => 'Français', 'icon' => asset('icons/french.svg')],
                'nb' => ['label' => 'Norsk', 'icon' => asset('icons/norway.svg')],
                'zh-Hant' => ['label' => '中文', 'icon' => asset('icons/china.svg')],
                'cs' => ['label' => 'Česky', 'icon' => asset('icons/czech.svg')],
            ];
        @endphp

        <div x-data="{ open: false }" @mouseleave="open = false" class="relative inline-block ml-2 z-50 text-left">
            <!-- Botón principal -->
            <button @click="open = !open"
                class="flex items-center gap-2 px-3 py-1.5 rounded-md shadow-sm bg-white dark:bg-themeBgDark text-gray-800 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 
                focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-themeBlue cursor-pointer translate-y-1">
                <img src="{{ $languages[App::getLocale()]['icon'] }}" alt="Bandera" class="w-5 h-5 rounded">
                <span class="text-sm font-medium">{{ $languages[App::getLocale()]['label'] }}</span>
                <svg class="w-4 h-4 ml-1 transition-transform duration-300"
                    :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Menú desplegable -->
            <div x-show="open" x-transition.origin.top.left @click.outside="open = false"
                class="absolute right-0 mt-2 w-44 rounded-md shadow-lg bg-white dark:bg-themeBgDark border border-gray-200 dark:border-gray-600 overflow-hidden ring-1 ring-black ring-opacity-5 focus:outline-none">
                @foreach ($languages as $locale => $info)
                    <a href="{{ LaravelLocalization::getLocalizedURL($locale, null, [], true) }}"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200
                            {{ App::getLocale() === $locale ? 'font-semibold bg-gray-100 dark:bg-gray-700' : '' }}">
                        <img src="{{ $info['icon'] }}" alt="{{ $info['label'] }}" class="w-5 h-5 rounded">
                        {{ $info['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

    </nav>

    <!-- Mobile Menu Button (XL and below) -->
    <div class="flex 2xl:hidden items-center space-x-4">
        <!-- Mobile Notifications -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="relative p-2 cursor-pointer">
                <x-icon name="bell" class="w-6 h-auto fill-none" />
                @if(auth()->user()->notifications->count() > 0)
                    <span class="absolute -top-0 right-1 bg-red-500 text-white text-xs rounded-full px-1">
                        {{ auth()->user()->notifications->count() }}
                    </span>
                @endif
            </button>

            <div x-cloak x-show="open" x-transition @click.outside="open = false"
                class="absolute -right-10 mt-2 w-72 dark:bg-themeBgDark bg-white border border-gray-300 shadow-xl rounded-md z-50">
                <ul class="max-h-64 overflow-y-auto" data-notification-list>
                    @forelse(auth()->user()->notifications as $notification)
                        <li class="flex flex-row justify-between items-start p-3 hover:bg-themeLightGray/20 cursor-default transition">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold">{{ $notification->title }}</span>
                                <span class="text-sm">{{ $notification->message}}</span>
                            </div>
                            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}"
                                class="form-eliminar-notificacion" data-id="{{ $notification->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-eliminar">
                                    <x-icon name="x" class="w-6 h-auto text-themeRed cursor-pointer" />
                                </button>
                            </form>
                        </li>
                    @empty
                        <li class="p-3 text-sm text-gray-500">{{ __('messages.navbar.no-notifications') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Mobile Theme Toggle -->
        <div x-data="{ rotating: false }">
            <button id="theme-toggle-mobile" 
                @click="darkMode = !darkMode; rotating = true; setTimeout(() => rotating = false, 600)"
                :class="{'rotate-[360deg]': rotating}"
                class="p-2 rounded-full cursor-pointer transition-transform duration-500 ease-in-out transform">
                <x-icon name="dark-light" class="w-6 h-auto" />
            </button>
        </div>

        <!-- Hamburger Menu Button -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 cursor-pointer">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Desktop Profile (XL and up) -->
    <nav class="hidden 2xl:flex flex-grow justify-end basis-0">
        @auth
            <a href="{{ route('profile.index') }}"
                class="flex flex-row items-center space-x-2 px-3 py-2 hover:bg-black/5 dark:hover:bg-white/9 
                                            transition border-b-2 border-transparent rounded hover:border-b-2 hover:border-b-themeBlue">
                <span>{{ auth()->user()->name }}</span>
                <img src="{{ auth()->user()->profile ? asset('storage/' . auth()->user()->profile) : 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/Windows_10_Default_Profile_Picture.svg/2048px-Windows_10_Default_Profile_Picture.svg.png' }}"
                    alt="Perfil" id="profileImage"
                    class="size-8 rounded-full border-2 border-themeBlue dark:border-white object-cover shadow-lg">
            </a>
        @endauth
    </nav>

    <!-- Mobile Menu Dropdown -->
    <div x-cloak x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0 translate-y-[-10px]" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" 
         x-transition:leave-end="opacity-0 translate-y-[-10px]" @click.outside="mobileMenuOpen = false"
         class="2xl:hidden w-full fixed inset-0 top-0 dark:bg-themeBgDark bg-white shadow-xl z-40 min-h-screen">
        
        <!-- Close Button -->
        <div class="flex justify-end p-4">
            <button @click="mobileMenuOpen = false" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="px-6 pb-6 space-y-6">
            <!-- Mobile Navigation Links -->
            <div class="space-y-3">
                <a href="{{ route('dashboard') }}" class="block px-4 py-3 text-lg rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    {{ __('messages.navbar.home') }}
                </a>
                <a href="{{ route('projects.index') }}" class="block px-4 py-3 text-lg rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    {{ __('messages.navbar.projects') }}
                </a>
                <a href="{{ route('job.offers.index') }}" class="block px-4 py-3 text-lg rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    {{ __('messages.navbar.job-offers') }}
                </a>
                <a href="{{ route('favorites.index') }}" class="block px-4 py-3 text-lg rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    {{ __('messages.navbar.favorites') }}
                </a>

                @auth
                    @php
                        $role = auth()->user()->role;
                    @endphp

                    @if(in_array($role, ['Usuario', 'Alumno']))
                        <a href="{{ route('projects.ownProjects') }}" class="block px-4 py-3 text-lg rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            {{ __('messages.navbar.my-projects') }}
                        </a>
                    @endif
                    @if($role === 'Profesor')
                        <a href="{{ route('school.projects.index') }}" class="block px-4 py-3 text-lg rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            {{ __('messages.navbar.school-projects') }}
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 text-lg rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            Panel de Administración
                        </a>
                    @endif
                    @if(in_array($role, ['Profesor', 'Empresa']))
                        <a href="{{ route('applications.index') }}" class="block px-4 py-3 text-lg rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            {{ __('messages.navbar.applications') }}
                        </a>
                        <a href="{{ route('job.offers.company.index') }}" class="block px-4 py-3 text-lg rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            {{ __('messages.navbar.my-job-offers') }}
                        </a>
                    @endif
                    @if($role === 'Admin')
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 text-lg rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            {{ __('messages.navbar.admin-panel') }}
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Mobile Language Selector -->
            <div x-data="{ open: false }" class="border-t border-gray-200 dark:border-gray-600 pt-6">
                <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-3 text-lg rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center gap-3">
                        <img src="{{ $languages[App::getLocale()]['icon'] }}" alt="Bandera" class="w-6 h-6">
                        <span>{{ $languages[App::getLocale()]['label'] }}</span>
                    </div>
                    <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" x-transition class="mt-3 ml-6 space-y-2">
                    @foreach ($languages as $locale => $info)
                        <a href="{{ LaravelLocalization::getLocalizedURL($locale, null, [], true) }}" 
                           class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors
                                  {{ App::getLocale() === $locale ? 'font-semibold bg-gray-100 dark:bg-gray-700' : '' }}">
                            <img src="{{ $info['icon'] }}" alt="{{ $info['label'] }}" class="w-5 h-5">
                            {{ $info['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Mobile Profile -->
            @auth
                <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                    <a href="{{ route('profile.index') }}" class="flex items-center space-x-4 px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <img src="{{ auth()->user()->profile ? asset('storage/' . auth()->user()->profile) : 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/Windows_10_Default_Profile_Picture.svg/2048px-Windows_10_Default_Profile_Picture.svg.png' }}"
                            alt="Perfil" class="w-10 h-10 rounded-full border-2 border-themeBlue dark:border-white object-cover">
                        <span class="text-lg">{{ auth()->user()->name }}</span>
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <!-- Desktop backdrop (mantiene la funcionalidad original) -->
    <div id="menu-backdrop" class="hidden xl:block absolute bg-black/5 dark:bg-white/9 border-b-2 border-b-themeBlue backdrop-blur-lg rounded translate-x-[var(--left)] translate-y-[var(--top)] left-0 top-0 w-[var(--width)]
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

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.form-eliminar-notificacion').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const id = this.dataset.id;
                const action = this.getAttribute('action');

                fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                })
                    .then(response => response.json())
                    .then(data => {
                        this.closest('li')?.remove();
                        const dashboardList = document.getElementById('dashboard-notification-list');
                        if (dashboardList && data.dashboardHtml !== undefined) {
                            dashboardList.innerHTML = data.dashboardHtml;
                        }

                        // Actualizar contador
                        const contador = document.getElementById('notification-count');
                        if (contador) {
                            if (data.notificationCount > 0) {
                                contador.textContent = data.notificationCount;
                                contador.classList.remove('hidden');
                            } else {
                                contador.classList.add('hidden');
                            }
                        }
                        // Comprobar si el menú desplegable está vacío también
                        const lista = document.querySelector('[data-notification-list]');
                        if (lista && lista.querySelectorAll('li').length === 0) {
                            lista.innerHTML = `<li class="p-3 text-sm text-gray-500">{{ __('messages.navbar.no-notifications') }}</li>`;
                        }
                    });


            });
        });
    });

</script>