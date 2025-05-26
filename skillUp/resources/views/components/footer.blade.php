<footer class="flex flex-col items-center gap-3 bg-themeBgDark w-full h-auto py-4 text-gray-300 mt-10">
    <a href="" class="pointer-events-none">
        <img src="{{ asset('icons/skill-up-logo.svg') }}" alt="Skill-Up" class="h-14">
    </a>

    <p class="text-sm mb-3">&copy; {{ date('Y') }} {{ __('messages.footer.credits') }}</p>
    <ul class="flex gap-4">
        <div class="flex gap-3  [&>a]:px-1 [&>a]:py-1 [&>a]:h-auto [&>a]:bg-themeDarkGray [&>a]:rounded-full [&>a]:hover:bg-themeLightGray [&>a]:transition-all [&>a]:duration-300 [&>a]:hover:text-themeBlue ">
            <a href="https://github.com/joonaayag">
                <x-icon name="github" />
            </a>
            <a href="https://www.instagram.com/joonayy_/">
                <x-icon name="instagram" />
            </a>
            <a href="https://x.com/home">
                <x-icon name="x" />
            </a>
            <a href="mailto:skillup.help.contact@gmail.com">
                <x-icon name="mail" />
            </a>
        </div>
    </ul>
</footer>