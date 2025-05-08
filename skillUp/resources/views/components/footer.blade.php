<footer class="flex flex-col items-center gap-3 bg-themeBgDark w-full h-auto py-4 text-gray-300">
    <a href="" class="pointer-events-none">
        <img src="{{ asset('icons/skill-up-logo.svg') }}" alt="Skill-Up" class="h-14">
    </a>

    <p class="text-sm mb-3">&copy; {{ date('Y') }} Skill-Up. Todos los derechos reservados.</p>
    <ul class="flex gap-4">
        <div class="flex">
            <a href="https://github.com/joonaayag/skill-up" class="w-10 h-auto">
                <x-icon name="github" />
            </a>
            <a href="#" class="w-10 h-auto">
                <x-icon name="instagram" />
            </a>
            <a href="#" class="w-10 h-auto">
                <x-icon name="x" />
            </a>
            <a href="mailto:joonaayag19@gmail.com" class="w-10 h-auto">
                <x-icon name="mail" />
            </a>
        </div>
    </ul>
</footer>