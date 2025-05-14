import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Configuración para producción
            buildDirectory: 'build', // Asegura que se construya en public/build
        }),
        tailwindcss(),
    ],
    build: {
        // Asegura que el manifest se genere correctamente
        manifest: true,
        outDir: 'public/build', // Directorio de salida explícito
        rollupOptions: {
            // Asegúrate de que las importaciones se resuelvan correctamente
            output: {
                manualChunks: undefined,
            },
        },
    },
});