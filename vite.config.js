import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js','resources/css/login.css',
                'resources/css/sidebar.css','resources/css/datatable.css'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
