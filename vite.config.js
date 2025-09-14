import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/assets/frontend/css/app.css',
                'resources/assets/frontend/css/stories_new_styles.css',
                'resources/assets/frontend/css/styles.css',
                'resources/assets/frontend/css/home.css',
                'resources/assets/frontend/js/app.js',
                'resources/assets/frontend/js/common.js',
                'resources/assets/frontend/js/story.js',
                'resources/assets/frontend/js/chapter.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: 'akaytruyen.new.local',
        port: 5173,
        strictPort: true,
        cors: true,
        hmr: {
            host: 'akaytruyen.new.local',
            protocol: 'http',
            port: 5173,
        },
    },
    
    
});
