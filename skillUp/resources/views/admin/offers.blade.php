@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-6 py-10">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">{{ __('messages.admin.offers.title') }}</h1>
        @if ($errors->any())
            <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="text-black dark:text-white">- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="overflow-x-auto rounded-lg shadow">
            <table
                class="min-w-full bg-white dark:bg-themeDarkGray text-sm text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 border dark:border-gray-700">ID</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.offers.table-title') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.offers.table-subtitle') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.offers.table-sector') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.offers.table-category') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.offers.table-state') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.offers.table-actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($offers as $offer)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $offer->id }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $offer->name }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $offer->subtitle }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">
                            @php
                                    $sectorMap = [
                                        'Agricultura/Medio ambiente' => 'sector-agri',
                                        'Arte/Cultura' => 'sector-art',
                                        'Automoción' => 'sector-aut',
                                        'Ciberseguridad' => 'sector-cyb',
                                        'Community Manager' => 'sector-comm',
                                        'Construcción' => 'sector-cons',
                                        'Coordinación Educativa' => 'sector-educ',
                                        'Diseño Gráfico' => 'sector-grap',
                                        'Electricidad y fontanería' => 'sector-elec',
                                        'Energía/Renovables' => 'sector-ener',
                                        'Farmacia' => 'sector-phar',
                                        'Finanzas y contabilidad' => 'sector-fina',
                                        'Fotografía/vídeo' => 'sector-photo',
                                        'Hostelería/turismo' => 'sector-hosp',
                                        'AI' => 'sector-ai',
                                        'Investigación/laboratorio' => 'sector-res',
                                        'Legal' => 'sector-leg',
                                        'Logística' => 'sector-log',
                                        'Mecánica' => 'sector-mec',
                                        'Medicina/Enfermería' => 'sector-med',
                                        'Nutrición' => 'sector-nut',
                                        'Operador Industrial' => 'sector-ind',
                                        'Orientación' => 'sector-ori',
                                        'Periodismo' => 'sector-jout',
                                        'Enseñanza' => 'sector-tea',
                                        'Psicología' => 'sector-psy',
                                        'Publicidad' => 'sector-adv',
                                        'Redes y Sistemas' => 'sector-net',
                                        'RRHH' => 'sector-hr',
                                        'Seguridad' => 'sector-sec',
                                        'SEO/SEM' => 'sector-seo',
                                        'Terapias/Rehabilitación' => 'sector-ther',
                                        'Traducción' => 'sector-trans',
                                        'Transporte/Entrega' => 'sector-transp',
                                        'Ventas' => 'sector-sal',
                                    ];

                                        $sectorkey = $sectorMap[$offer->sector_category] ?? null;
                                    @endphp

                                    @if ($sectorkey)
                                            {{ __('messages.job-offers.' . $sectorkey) }}
                                    @endif
                                </td>
                            <td class="px-4 py-3 border dark:border-gray-700">
                                    @php
                                        $categoryMap = [
                                            'Administración y negocio' => 'option-admin',
                                            'Ciencia y salud' => 'option-science',
                                            'Comunicación' => 'option-comunication',
                                            'Diseño y comunicación' => 'option-design',
                                            'Educación' => 'option-education',
                                            'Industria' => 'option-industry',
                                            'Otro' => 'option-other',
                                            'Tecnología y desarrollo' => 'option-tec',
                                        ];

                                        $categoryKey = $categoryMap[$offer->general_category] ?? null;
                                    @endphp

                                    @if ($categoryKey)
                                   {{ __('messages.projects.' . $categoryKey) }}
                                    @endif
                                </td>
                            <td class="px-4 py-3 border dark:border-gray-700">
                                {{ __('messages.company-offers.' . ($offer->state == 'Abierta' ? 'open' : 'close')) }}
                            </td>
                            <td class="px-4 py-3 border dark:border-gray-700 whitespace-nowrap space-x-3">

                                <div x-data="{ openEdit: false }" class="inline-block" x-cloak>
                                    <button @click="openEdit = true"
                                        class="bg-themeBlue/80 border-2 border-themeBlue/80 hover:bg-themeBlue text-white font-semibold py-2 px-4 rounded transition cursor-pointer">{{__('messages.button.edit')}}</button>

                                    <x-modal :show="'openEdit'" @click.outside="openEdit = false">
                                        <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.admin.offers.edit') }}</x-heading>

                                        <form action="{{ route('admin.offers.update', $offer->id) }}" method="POST"
                                            class="space-y-4">
                                            @csrf
                                            @method('PUT')

                                            <div>
                                                <label class="block text-sm font-medium">{{ __('messages.admin.offers.label-title') }}</label>
                                                <input type="text" id="name" name="name" value="{{ $offer->name }}"
                                                    class="w-full border rounded px-3 py-2 dark:bg-themeDark dark:text-white dark:border-gray-600"
                                                    required>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium">{{ __('messages.admin.offers.label-subtitle') }}</label>
                                                <input type="text" id="subtitle" name="subtitle" value="{{ $offer->subtitle }}"
                                                    class="w-full border rounded px-3 py-2 dark:bg-themeDark dark:text-white dark:border-gray-600">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium">{{ __('messages.admin.offers.label-description') }}</label>
                                                <textarea name="description" id="description" rows="4"
                                                    class="w-full border rounded px-3 py-2 dark:bg-themeDark dark:text-white dark:border-gray-600 resize-none"
                                                    required>{{ $offer->description }}</textarea>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium">{{ __('messages.admin.offers.label-sector') }}</label>
                                                    <select name="sector_category" id="sector_category" required
                                                        class="w-full border-themeLightGray rounded px-4 py-2 dark:bg-themeBgDark bg-white dark:bg-themeBgD">
                                                        <option value="" {{ old('sector_category') === null ? 'selected' : '' }}>
                                                            {{ __('messages.select') }}
                                                        </option>

                                                        @foreach (array_keys($sectorMap) as $sector)
                                                            <option value="{{ $sector }}" {{ old('sector_category') === $sector ? 'selected' : '' }}>
                                                                {{ __('messages.job-offers.' . $sectorMap[$sector]) }}
                                                            </option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium">{{ __('messages.admin.offers.label-category') }}</label>
                                                    <select name="general_category" id="general_category" required
                                                        class="w-full px-3 py-2 dark:bg-themeBgDark rounded border border-themeLightGray">
                                                        <option value="Administración y negocio" {{ old('general_category') == 'Administración y negocio' ? 'selected' : '' }}>{{ __('messages.projects.option-admin') }}</option>
                                                        <option value="Ciencia y salud" {{ old('general_category') == 'Ciencia y salud' ? 'selected' : '' }}>
                                                            {{ __('messages.projects.option-science') }}
                                                        </option>
                                                        <option value="Comunicación" {{ old('general_category') == 'Comunicación' ? 'selected' : '' }}>
                                                            {{ __('messages.projects.option-comunication') }}
                                                        </option>
                                                        <option value="Diseño y comunicación" {{ old('general_category') == 'Diseño y comunicación' ? 'selected' : '' }}>{{ __('messages.projects.option-design') }}</option>
                                                        <option value="Educación" {{ old('general_category') == 'Educación' ? 'selected' : '' }}>
                                                            {{ __('messages.projects.option-education') }}
                                                        </option>
                                                        <option value="Industria" {{ old('general_category') == 'Industria' ? 'selected' : '' }}>
                                                            {{ __('messages.projects.option-industry') }}
                                                        </option>
                                                        <option value="Otro" {{ old('general_category') == 'Otro' ? 'selected' : '' }}>
                                                            {{ __('messages.projects.option-other') }}
                                                        </option>
                                                        <option value="Tecnología y desarrollo" {{ old('general_category') == 'Tecnología y desarrollo' ? 'selected' : '' }}>{{ __('messages.projects.option-tec') }}</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium">{{ __('messages.admin.offers.label-state') }}</label>
                                                <select name="state" id="state"
                                                    class="w-full border rounded px-3 py-2 dark:bg-themeDark dark:bg-themeBgDark dark:text-white dark:border-gray-600"
                                                    required>
                                                    <option value="abierta" {{ $offer->state === 'abierta' ? 'selected' : '' }}>
                                                        {{ __('messages.admin.offers.status-open') }}</option>
                                                    <option value="cerrada" {{ $offer->state === 'cerrada' ? 'selected' : '' }}>
                                                        {{ __('messages.admin.offers.status-close') }}</option>
                                                </select>
                                            </div>

                                            <div class="mt-6 flex justify-end gap-4">
                                                <button type="button" @click="openEdit = false"
                                                    class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                                                    {{ __('messages.button.cancel') }}
                                                </button>
                                                <button type="submit"
                                                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 cursor-pointer">
                                                    {{ __('messages.button.save-changes') }}
                                                </button>
                                            </div>
                                        </form>
                                    </x-modal>
                                </div>

                                <div x-data="{ openDelete: false }" class="inline-block" x-cloak>
                                    <button @click="openDelete = true"
                                        class="bg-red-500 border-2 border-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded transition cursor-pointer">{{ __('messages.button.delete') }}</button>

                                    <x-modal :show="'openDelete'" @click.outside="openDelete = false">
                                        <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.admin.offers.delete-confirm') }}</x-heading>
                                        <p class="mb-4 text-gray-600 dark:text-gray-300 break-words">
                                            {{ __('messages.admin.offers.delete-text-1') }} <strong>{{ $offer->name }}</strong>{{ __('messages.admin.offers.delete-text-2') }}.
                                        </p>
                                        <div class="flex justify-end gap-4">
                                            <button @click="openDelete = false"
                                                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-sm rounded hover:bg-gray-300 dark:hover:bg-gray-600 cursor-pointer">
                                                {{ __('messages.button.cancel') }}
                                            </button>
                                            <form action="{{ route('admin.offers.destroy', $offer->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-4 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700 cursor-pointer">
                                                    {{ __('messages.button.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </x-modal>

                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-5 text-center text-gray-500 dark:text-gray-300">
                                {{ __('messages.admin.offers.no-offers') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div x-data="{ showOffer: false }" class="inline-block mt-5" x-cloak>
            <button @click="showOffer = true"
                class="flex  gap-2 items-center bg-themeBlue/80 border-2 border-themeBlbg-themeBlue/80 hover:bg-themeBlue text-white font-semibold py-2 px-4 rounded-lg transition cursor-pointer"><x-icon
                    name="plus" class="w-5 h-auto" /> {{ __('messages.button.create') }}</button>

            <x-modal :show="'showOffer'" @close="showOffer = false">
                <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.admin.offers.create') }}</x-heading>
                <form action="{{ route('admin.job.offers.store') }}" method="POST"
                class="space-y-4 [&>select]:border-2  [&>select]:border-themeLightGray [&>input]:outline-0 [&>textarea]:outline-0">
                @csrf

                <x-label for="title">{{ __('messages.admin.offers.label-title') }}</x-label>
                <x-inputtext type="text" name="name" id="name" value="{{ old('name') }}" required />

                <x-label for="subtitle">{{ __('messages.admin.offers.label-subtitle') }}</x-label>
                <x-inputtext type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}" required />

                <x-label for="description">{{ __('messages.admin.offers.label-description') }}</x-label>
                <x-textarea name="description" id="description" required>{{ old('description') }}</x-textarea>

                <x-label for="sector_category">{{ __('messages.admin.offers.label-sector') }}</x-label>
                <select name="sector_category" id="sector_category" required
                    class="w-full border-themeLightGray rounded px-4 py-2 dark:bg-themeBgDark bg-white dark:bg-themeBgD">
                    @foreach (['Agricultura/Medio ambiente', 'Arte/Cultura', 'Automoción', 'Ciberseguridad', 'Community Manager', 'Construcción', 'Coordinación Educativa', 'Diseño Gráfico', 'Electricidad y fontanería', 'Energía/Renovables', 'Farmacia', 'Finanzas y contabilidad', 'Fotografía/vídeo', 'Hostelería/turismo', 'AI', 'Investigación/laboratorio', 'Legal', 'Logística', 'Mecánica', 'Medicina/Enfermería', 'Nutrición', 'Operador Industrial', 'Orientación', 'Periodismo', 'Enseñanza', 'Psicología', 'Publicidad', 'Redes y Sistemas', 'RRHH', 'Seguridad', 'SEO/SEM', 'Terapias/Rehabilitación', 'Traducción', 'Transporte/Entrega', 'Ventas'] as $sector)
                        <option value="{{ $sector }}" {{ old('sector_category') === $sector ? 'selected' : '' }}>
                            {{ $sector }}
                        </option>
                    @endforeach
                </select>

                <x-label for="general_category">{{ __('messages.admin.offers.label-category') }}</x-label>
                <select name="general_category" required
                    class="w-full px-3 py-2 dark:bg-themeBgDark rounded border border-themeLightGray">
                    <option value="Administración y negocio" {{ old('general_category') == 'Administración y negocio' ? 'selected' : '' }}>{{ __('messages.projects.option-admin') }}</option>
                    <option value="Ciencia y salud" {{ old('general_category') == 'Ciencia y salud' ? 'selected' : '' }}>
                        {{ __('messages.projects.option-science') }}
                    </option>
                    <option value="Comunicación" {{ old('general_category') == 'Comunicación' ? 'selected' : '' }}>
                        {{ __('messages.projects.option-comunication') }}
                    </option>
                    <option value="Diseño y comunicación" {{ old('general_category') == 'Diseño y comunicación' ? 'selected' : '' }}>{{ __('messages.projects.option-design') }}</option>
                    <option value="Educación" {{ old('general_category') == 'Educación' ? 'selected' : '' }}>
                        {{ __('messages.projects.option-education') }}
                    </option>
                    <option value="Industria" {{ old('general_category') == 'Industria' ? 'selected' : '' }}>
                        {{ __('messages.projects.option-industry') }}
                    </option>
                    <option value="Otro" {{ old('general_category') == 'Otro' ? 'selected' : '' }}>
                        {{ __('messages.projects.option-other') }}
                    </option>
                    <option value="Tecnología y desarrollo" {{ old('general_category') == 'Tecnología y desarrollo' ? 'selected' : '' }}>{{ __('messages.projects.option-tec') }}</option>
                </select>

                <x-label for="state">{{ __('messages.admin.offers.label-state') }}</x-label>
                <select name="state" required
                    class="w-full border-themeLightGray rounded px-4 py-2 bg-white dark:bg-themeBgDark outline-0">
                    <option value="abierta" @selected(old('state') == 'abierta')>{{ __('messages.admin.offers.status-open') }}</option>
                    <option value="cerrada" @selected(old('state') == 'cerrada')>{{ __('messages.admin.offers.status-close') }}</option>
                </select>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" @click="showOffer = false"
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

    </div>
    <script>
        document.getElementById('mi-form').addEventListener('submit', function (event) {
            const formData = {
                name: document.getElementById('name')?.value.trim() || '',
                subtitle: document.getElementById('subtitle')?.value.trim() || '',
                description: document.getElementById('description')?.value.trim() || '',
                sector_category: document.getElementById('sector_category')?.value || '',
                general_category: document.getElementsByName('general_category')[0]?.value || '',
                state: document.getElementsByName('state')[0]?.value || ''
            };

            const errors = {};

            if (!formData.name) {
                errors.name = "{{ __('messages.errors.name.required') }}";
            } else if (formData.name.length > 40) {
                errors.name = "{{ __('messages.errors.name.max') }}";
            }

            if (formData.subtitle && formData.subtitle.length > 255) {
                errors.subtitle = "{{ __('messages.errors.subtitle.max') }}";
            }

            if (!formData.description) {
                errors.description = "{{ __('messages.errors.description.required') }}";
            }

            const validSectors = [
                'Agricultura/Medio ambiente', 'Arte/Cultura', 'Automoción', 'Ciberseguridad', 'Community Manager', 'Construcción',
                'Coordinación Educativa', 'Diseño Gráfico', 'Electricidad y fontanería', 'Energía/Renovables', 'Farmacia', 'Finanzas y contabilidad',
                'Fotografía/vídeo', 'Hostelería/turismo', 'AI', 'Investigación/laboratorio', 'Legal', 'Logística', 'Mecánica', 'Medicina/Enfermería',
                'Nutrición', 'Operador Industrial', 'Orientación', 'Periodismo', 'Enseñanza', 'Psicología', 'Publicidad', 'Redes y Sistemas',
                'RRHH', 'Seguridad', 'SEO/SEM', 'Terapias/Rehabilitación', 'Traducción', 'Transporte/Entrega', 'Ventas'
            ];
            if (!formData.sector_category) {
                errors.sector_category = "{{ __('messages.errors.sector_offer.required') }}";
            } else if (!validSectors.includes(formData.sector_category)) {
                errors.sector_category = "{{ __('messages.errors.sector_offer.in') }}";
            }

            const validCategories = [
                'Administración y negocio', 'Ciencia y salud', 'Comunicación', 'Diseño y comunicación',
                'Educación', 'Industria', 'Otro', 'Tecnología y desarrollo'
            ];
            if (!formData.general_category) {
                errors.general_category = "{{ __('messages.errors.sector.required') }}";
            } else if (!validCategories.includes(formData.general_category)) {
                errors.general_category = "{{ __('messages.errors.sector.in') }}";
            }

            if (!formData.state) {
                errors.state = "{{ __('messages.errors.state.required') }}";
            } else if (!['abierta', 'cerrada'].includes(formData.state)) {
                errors.state = "{{ __('messages.errors.state.in') }}";
            }

            const errorBox = document.getElementById('form-errors');
            const errorList = document.getElementById('error-list');

            if (Object.keys(errors).length > 0) {
                event.preventDefault();

                errorList.innerHTML = '';
                errorBox.classList.remove('hidden');

                Object.values(errors).forEach(msg => {
                    const li = document.createElement('li');
                    li.textContent = msg;
                    errorList.appendChild(li);
                });
            } else {
                errorBox.classList.add('hidden');
            }
        });
    </script>
@endsection