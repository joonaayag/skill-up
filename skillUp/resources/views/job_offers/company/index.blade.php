@extends('layouts.app')

@section('title', 'Ofertas de Empleo')

@section('content')
    <x-heading level="h1" class="mb-10">{{ __('messages.company-offers.title') }}</x-heading>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

        <form id="filters-input" method="GET" action="{{ route('job.offers.company.index') }}" class="mb-6 space-y-2">
            <div class="grid grid-cols-[250px_1fr] gap-20 p-4 dark:text-themeLightGray">
                <aside
                    class="bg-white dark:bg-themeBgDark rounded-lg border-2 border-themeLightGray shadow px-4 py-5 space-y-4">
                    <x-heading level="h4"><strong>{{ __('messages.job-offers.category') }}</strong></x-heading>
                    @php
                        $generalCategories = [
                        __('messages.job-offers.category-admin'),
                        __('messages.job-offers.category-science'),
                        __('messages.job-offers.category-comunication'),
                        __('messages.job-offers.category-design'),
                        __('messages.job-offers.category-education'),
                        __('messages.job-offers.category-industry'),
                        __('messages.job-offers.category-other'),
                        __('messages.job-offers.category-tec'),
                    ];
                    @endphp
                    <div class="space-y-1">
                        @foreach ($generalCategories as $cat)
                            <label class="block">
                                <input type="checkbox" name="general_category[]" value="{{ $cat }}"
                                    @checked(is_array(request('general_category')) && in_array($cat, request('general_category')))>
                                {{ $cat }}
                            </label>
                        @endforeach
                    </div>

                    <x-heading level="h4" class="mt-4"><strong>Sector</strong></x-heading>
                    @php
                        $sectorCategories = [
                        __('messages.job-offers.sector-agri'),
                        __('messages.job-offers.sector-art'),
                        __('messages.job-offers.sector-aut'),
                        __('messages.job-offers.sector-cyb'),
                        __('messages.job-offers.sector-comm'),
                        __('messages.job-offers.sector-cons'),
                        __('messages.job-offers.sector-educ'),
                        __('messages.job-offers.sector-grap'),
                        __('messages.job-offers.sector-elec'),
                        __('messages.job-offers.sector-ener'),
                        __('messages.job-offers.sector-phar'),
                        __('messages.job-offers.sector-fina'),
                        __('messages.job-offers.sector-photo'),
                        __('messages.job-offers.sector-hosp'),
                        __('messages.job-offers.sector-ai'),
                        __('messages.job-offers.sector-res'),
                        __('messages.job-offers.sector-leg'),
                        __('messages.job-offers.sector-log'),
                        __('messages.job-offers.sector-mec'),
                        __('messages.job-offers.sector-med'),
                        __('messages.job-offers.sector-nut'),
                        __('messages.job-offers.sector-ind'),
                        __('messages.job-offers.sector-ori'),
                        __('messages.job-offers.sector-jout'),
                        __('messages.job-offers.sector-tea'),
                        __('messages.job-offers.sector-psy'),
                        __('messages.job-offers.sector-adv'),
                        __('messages.job-offers.sector-net'),
                        __('messages.job-offers.sector-hr'),
                        __('messages.job-offers.sector-sec'),
                        __('messages.job-offers.sector-seo'),
                        __('messages.job-offers.sector-ther'),
                        __('messages.job-offers.sector-trans'),
                        __('messages.job-offers.sector-transp'),
                        __('messages.job-offers.sector-sal'),

                    ];
                    @endphp
                    <div class="space-y-1">
                        @foreach ($sectorCategories as $sector)
                            <label class="block">
                                <input type="checkbox" name="sector_category[]" value="{{ $sector }}"
                                    @checked(is_array(request('sector_category')) && in_array($sector, request('sector_category')))>
                                {{ $sector }}
                            </label>
                        @endforeach
                    </div>
                </aside>

                <main class="space-y-4">
                    <div
                        class="flex flex-wrap gap-2 h-12 [&>input]:h-full [&>select]:h-full [&>input]:bg-white dark:[&>input]:bg-themeBgDark [&>select]:bg-white dark:[&>select]:bg-themeBgDark
                                                 [&>input]:rounded-lg [&>select]:rounded-lg [&>input]:border-2 [&>input]:border-themeLightGray [&>select]:border-2 [&>select]:border-themeLightGray [&>select]:px-4 [&>input]:px-4 [&>input]:outline-0">
                        <input type="text" name="name" placeholder="{{ __('messages.job-offers.placeholder-title') }}" value="{{ request('name') }}"
                            class="input" />
                        <select name="order">
                            <option value="">{{ __('messages.job-offers.order-by') }}</option>
                            <option value="name" @selected(request('order') == 'name')>{{ __('messages.job-offers.title') }}</option>
                            <option value="general_category" @selected(request('order') == 'general_category')>{{ __('messages.job-offers.category') }}</option>
                            <option value="created_at" @selected(request('order') == 'created_at')>{{ __('messages.job-offers.order-date') }}</option>
                        </select>

                        <select name="state">
                            <option value="">Estado</option>
                            <option value="Abierta" @selected(request('state') == 'Abierta')>{{ __('messages.company-offers.open') }}</option>
                            <option value="Cerrada" @selected(request('state') == 'Cerrada')>{{ __('messages.company-offers.close') }}</option>
                        </select>
                        <select name="direction">
                            <option value="asc" @selected(request('direction') == 'asc')>Asc</option>
                            <option value="desc" @selected(request('direction') == 'desc')>Desc</option>
                        </select>

                    </div>
                    <ul class="grid grid-cols-3 gap-10">
                        @forelse ($offers as $offer)
                            <a href="{{ route('job.offers.show', $offer->id) }}">
                                <x-card class="h-full hover:border-themeBlue hover:scale-101 transition">
                                    <li class="flex flex-col h-full ">
                                        <x-tags class="mb-1">{{ $offer->general_category }}</x-tags>
                                        <x-heading level="h3" class="mb-1">{{ $offer->name }}</x-heading>
                                        <x-heading level="h4" class="mb-1">{{ $offer->sector_category }}</x-heading>
                                        @if($offer->subtitle)
                                            <p><strong>{{ Str::limit($offer->subtitle, 20) }}</strong></p>
                                        @endif

                                        @php
                                            $favorite = auth()->user()->favorites()
                                                ->where('type', 'oferta')
                                                ->where('reference_id', $offer->id);
                                        @endphp

                                        <div class="flex flex-row justify-between items-center mt-2">
                                            <div class="flex flex-row gap-3">
                                                <p class="px-3 py-1 rounded-full {{ $offer->state === 'Abierta' ? 'bg-themeBlue text-white' : 'bg-themeRed text-white' }}">{{ $offer->state }}</p>
                                               @if ($offer->company_id !== auth()->id())
                                                @if ($favorite)
                                                    <form action="{{ route('favorites.destroy', $favorite->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"><x-icon name="filled-heart" class="w-5 h-auto cursor-pointer" /></button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('favorites.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="type" value="oferta">
                                                        <input type="hidden" name="reference_id" value="{{ $offer->id }}">
                                                        <button type="submit"><x-icon name="heart" class="w-5 h-auto cursor-pointer" /></button>
                                                    </form>
                                                @endif
                                            @endif


                                                <p class="flex items-center justify-center gap-1"><x-icon name="graphic" class="w-4 h-auto" />{{ $offer->views }}</p>
                                            </div>
                                            <span class="text-sm">{{ __('messages.company-offers.you') }}</span>
                                        </div>

                                    </li>
                                </x-card>
                            </a>
                        @empty
                            <p>{{ __('messages.company-offers.no-offers') }}</p>
                        @endforelse
                    </ul>

                </main>
            </div>
        </form>

        <div x-data="{ showModal: false }" x-cloak
            x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))" class="relative z-10">

            <button @click="showModal = true"
                class="fixed bottom-6 right-6 p-2 bg-themeBlue text-white rounded-full shadow-lg hover:bg-themeHoverBlue transition cursor-pointer">
                <x-icon size="6" name="plus" />
            </button>

            <x-modal>
                <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.job-offers.new-offer') }}</x-heading>
                <form action="{{ route('job.offers.store') }}" method="POST"
                    class="space-y-4 [&>select]:border-2  [&>select]:border-themeLightGray [&>input]:outline-0 [&>textarea]:outline-0">
                    @csrf

                    <x-label for="title">{{ __('messages.job-offers.label-title') }}</x-label>
                    <x-inputtext type="text" name="name" id="name" required />

                    <x-label for="subtitle">{{ __('messages.job-offers.label-subtitle') }}</x-label>
                    <x-inputtext type="text" name="subtitle" id="subtitle" required />

                    <x-label for="description">{{ __('messages.job-offers.label-description') }}</x-label>
                    <x-textarea name="description" id="description" required></x-textarea>

                    <x-label for="sector_category">{{ __('messages.job-offers.label-sector') }}</x-label>
                    <x-inputtext type="text" name="sector_category" id="sector_category" required />

                    <x-label for="general_category">{{ __('messages.job-offers.label-category') }}</x-label>
                    <x-inputtext type="text" name="general_category" id="general_category" required />

                    <x-label for="state">{{ __('messages.job-offers.label-state') }}</x-label>
                    <select name="state" required
                        class="w-full border-themeLightGray rounded px-4 py-2 bg-white dark:bg-themeBgDark outline-0">
                        <option value="abierta">{{ __('messages.job-offers.state-open') }}</option>
                        <option value="cerrada">{{ __('messages.job-offers.state-close') }}</option>
                    </select>

                    <div class="flex justify-end gap-3 mt-4">
                    <button type="button" @click="showModal = false"
                        class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                        {{ __('messages.button.cancel') }}
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-themeBlue text-white rounded hover:bg-themeBlue/80 transition cursor-pointer">
                        {{ __('messages.button.save') }}
                    </button>
                </div>
                </form>
            </x-modal>


        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('filters-input');

                if (form) {
                    const inputs = form.querySelectorAll('input, select');

                    inputs.forEach(input => {
                        input.addEventListener('change', () => {
                            form.submit();
                        });

                        if (input.tagName === 'INPUT') {
                            input.addEventListener('keyup', () => {
                                clearTimeout(input._timeout);
                                input._timeout = setTimeout(() => form.submit(), 1000);
                            });
                        }
                    });
                }
            });

        </script>



@endsection