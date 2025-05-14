@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <x-heading level="h1" class="mb-10">Hola, {{ auth()->user()->name }}</x-heading>

    <div class="grid grid-cols-3 gap-14 h-full mb-24 ">
        <x-card>
            <x-heading level="h3" class="mb-8">Proyectos destacados</x-heading>
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
                    <p class="mt-6 text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">No existen proyectos
                        destacados por el momento.</p>
                @endif
            </div>
        </x-card>

        <div class="grid grid-rows-5 gap-8">
            <x-card class="row-span-2 h-full">
                <x-heading level="h3" class="mb-8">Tus proyectos</x-heading>
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
                        <p class="mt-6 text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">No tienes proyectos
                            propios por el momento.</p>
                    @endif
                </div>
            </x-card>
            <x-card class="row-span-3 h-full">
                <x-heading level="h3" class="mb-8">Últimas ofertas de empleo</x-heading>
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
                        <p class="mt-6 text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">No hay ofertas
                            disponibles por el momento.</p>
                    @endif
                </div>
            </x-card>
        </div>

        <div class="grid grid-rows-7 gap-8 -mt-20">
            <div
                class="row-span-3 h-full [&>div]:h-full [&>div]:bg-white [&>div]:border-2 [&>div]:border-themeLightGray [&>div]:rounded-lg dark:[&>div]:bg-themeBgDark">
                <div class="relative">
                    <img src="{{ auth()->user()->banner ? asset('storage/' . auth()->user()->banner) : 'https://i.pinimg.com/736x/b6/ef/40/b6ef40f2cd4436568d718f150abefca6.jpg' }}"
                        alt="Fondo" class="w-full h-30 object-cover" id="bannerImage">
                    <div class="absolute top-20 left-1/6 transform -translate-x-1/2">
                        <img src="{{ auth()->user()->foto_perfil ? asset('storage/' . auth()->user()->foto_perfil) : 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/Windows_10_Default_Profile_Picture.svg/2048px-Windows_10_Default_Profile_Picture.svg.png' }}"
                            alt="Perfil" id="profileImage"
                            class="h-18 w-18 rounded-full border-4 border-white object-cover shadow-lg">
                    </div>
                    <div class="px-3 mt-8">
                        <x-heading
                            level="h3">{{ ucfirst(auth()->user()->name) . ' ' . ucfirst(auth()->user()->last_name) }}</x-heading>
                        @if (auth()->user()->role === 'alumno')
                            <p>Estudiante de {{ auth()->user()->detail->educational_center }}</p>
                        @else
                            <p>{{ ucfirst(auth()->user()->role) }}</p>
                        @endif
                        <p>Ciudad</p>
                    </div>
                </div>
            </div>
            <x-card class="row-span-4 h-full">
                @if($notifications->count())
                    <x-heading level="h3" class="mb-8">Tus notificaciones recientes</x-heading>
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
                    <p class="mt-6 text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">No tienes notificaciones
                        por el momento.</p>
                @endif
            </x-card>
        </div>

    </div>


    <div x-data="chatbot()" class="fixed bottom-4 right-4 z-50">

        <button  @click="toggle"
            class="bg-themeBlue text-white rounded-full p-3 shadow-lg hover:bg-blue-700 cursor-pointer transition">
            💬
        </button>


        <div x-show="open" @click.outside="open = false" x-transition class="mt-2 w-72 dark:bg-themeBgDark bg-white border rounded-lg shadow-xl p-4">
            <h2 class="text-sm font-semibold mb-2">Asistente SkillUp</h2>
            <div class="h-48 overflow-y-auto text-sm space-y-2 mb-2" id="chat-window">
                <template x-for="msg in messages" :key="msg . id">
                    <div :class="msg . from === 'bot' ? 'text-left text-gray-700' : 'text-right text-themebg-themeBlue'">
                        <span class="text-themeLightGray" x-text="msg.text"></span>
                    </div>
                </template>
            </div>

            <form @submit.prevent="send">
                <input x-model="input" type="text" class="w-full border px-2 py-1 rounded text-sm"
                    placeholder="Escribe algo...">
            </form>
        </div>
    </div>
    <script>
        function chatbot() {
            return {
                open: false,
                input: '',
                messages: [
                    { id: 1, text: '¡Hola! Soy el asistente de SkillUp. Puedes preguntarme cosas como:', from: 'bot' },
                    { id: 2, text: '• ¿Dónde veo los proyectos?\n• ¿Cómo aplico a una puesto de una empresa?\n• ¿Dónde están las ofertas de trabajo?', from: 'bot' }
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

                    let response = 'No entendí eso. ¿Puedes repetirlo?';
                    const txt = this.input.toLowerCase();

                    if (
                        txt.includes('proyecto') ||
                        txt.includes('proyectos') ||
                        txt.includes('ver proyectos') ||
                        txt.includes('dónde están los proyectos') ||
                        txt.includes('project')
                    ) {
                        response = 'Puedes ver los proyectos en la sección "Proyectos" del menú.';
                    } else if (
                        txt.includes('registrar') ||
                        txt.includes('registro') ||
                        txt.includes('crear cuenta') ||
                        txt.includes('registrarse') ||
                        txt.includes('cómo me registro')
                    ) {
                        response = 'Para registrarte, haz clic en "Registrarse" arriba a la derecha.';
                    } else if (
                        txt.includes('oferta') ||
                        txt.includes('ofertas') ||
                        txt.includes('trabajo') ||
                        txt.includes('empleo') ||
                        txt.includes('vacantes') ||
                        txt.includes('buscar trabajo')
                    ) {
                        response = 'Puedes ver las ofertas laborales en la sección "Ofertas".';
                    } else if (
                        txt.includes('aplico') ||
                        txt.includes('aplicar') ||
                        txt.includes('inscribirme') ||
                        txt.includes('puesto') ||
                        txt.includes('cómo postularme') ||
                        txt.includes('me interesa una oferta')
                    ) {
                        response = 'Puedes aplicar a una oferta laboral accediendo a ella desde la sección "Ofertas".';
                    } else if (
                        txt.includes('empresa') ||
                        txt.includes('subir oferta') ||
                        txt.includes('publicar trabajo') ||
                        txt.includes('soy empresa')
                    ) {
                        response = 'Si eres una empresa, puedes registrarte y luego publicar ofertas laborales desde tu panel.';
                    } else if (
                        txt.includes('no puedo entrar') ||
                        txt.includes('problema para entrar') ||
                        txt.includes('olvidé mi contraseña') ||
                        txt.includes('no recuerdo mi clave')
                    ) {
                        response = 'Puedes restablecer tu contraseña haciendo clic en "¿Olvidaste tu contraseña?" en la página de login.';
                    } else if (
                        txt.includes('cómo funciona') ||
                        txt.includes('qué es esto') ||
                        txt.includes('para qué sirve') ||
                        txt.includes('qué puedo hacer aquí')
                    ) {
                        response = 'SkillUp es una plataforma para conectar estudiantes, profesorado y empresas a través de proyectos y oportunidades laborales.';
                    } else if (
                        txt.includes('hola') ||
                        txt.includes('buenas') ||
                        txt.includes('hey') ||
                        txt.includes('qué tal') ||
                        txt.includes('saludos')
                    ) {
                        response = '¡Hola! ¿En qué puedo ayudarte hoy? 😊';
                    } else if (
                        txt.includes('gracias') ||
                        txt.includes('muchas gracias') ||
                        txt.includes('te lo agradezco') ||
                        txt.includes('mil gracias') ||
                        txt.includes('thank you')
                    ) {
                        response = '¡De nada! Estoy aquí para ayudarte. 🙌';
                    } else if (
                        txt.includes('adiós') ||
                        txt.includes('hasta luego') ||
                        txt.includes('nos vemos') ||
                        txt.includes('chao') ||
                        txt.includes('bye')
                    ) {
                        response = '¡Hasta pronto! 👋 Que tengas un gran día.';
                    } else if (
                        txt.includes('estás ahí') ||
                        txt.includes('me escuchas') ||
                        txt.includes('puedes ayudarme') ||
                        txt.includes('alguien responde') ||
                        txt.includes('hay alguien')
                    ) {
                        response = 'Sí, estoy aquí para ayudarte 🤖. Pregúntame lo que necesites.';
                    } else if (
                        txt.includes('no sé') ||
                        txt.includes('estoy perdido') ||
                        txt.includes('no entiendo') ||
                        txt.includes('me puedes guiar') ||
                        txt.includes('qué hago')
                    ) {
                        response = 'No te preocupes, dime qué estás buscando y te ayudo encantado.';
                    } else if (
                        txt.includes('que tal') ||
                        txt.includes('como estas')

                    ) {
                        response = 'Estoy funcionando perfectamente, gracias por preguntar. Soy un asistente virtual sin sentimientos pero estoy aquí para ayudarte en lo que necesites.';
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