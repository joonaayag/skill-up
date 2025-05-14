import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            url: process.env.APP_URL || 'https://skill-up-production-7fdb.up.railway.app',
            buildDirectory: 'build',
        }),
        tailwindcss(),
    ],
    server: {
        https: true,
    },
});
