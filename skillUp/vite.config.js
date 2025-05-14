import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            url:'https://skill-up-production-7fdb.up.railway.app',
            https: true,
        }),
        tailwindcss(),
    ],
    server: {
        https: true,
    },
    build: {
        assetsDir: 'build/assets',
        manifest: true,
    },
});
