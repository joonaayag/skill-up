<footer class="flex flex-col items-center gap-3 bg-themeBgDark w-full h-auto py-4 text-gray-300">
    <a href="" class="pointer-events-none">
        <img src="{{ asset('icons/skill-up-logo.svg') }}" alt="Skill-Up" class="h-14">
    </a>

    <p class="text-sm mb-3">&copy; {{ date('Y') }} Skill-Up. Todos los derechos reservados.</p>
    <ul class="flex gap-4">
        <div class="flex gap-3  [&>a]:px-1 [&>a]:py-1 [&>a]:h-auto [&>a]:bg-themeDarkGray [&>a]:rounded-full [&>a]:hover:bg-themeLightGray [&>a]:transition-all [&>a]:duration-300 [&>a]:hover:text-themeBlue ">
            <a href="https://github.com/joonaayag/skill-up">
                <x-icon name="github" />
            </a>
            <a href="#">
                <x-icon name="instagram" />
            </a>
            <a href="#">
                <x-icon name="x" />
            </a>
            <a href="mailto:joonaayag19@gmail.com">
                <x-icon name="mail" />
            </a>
        </div>
    </ul>
</footer>