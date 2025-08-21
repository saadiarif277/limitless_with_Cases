import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    // server: {
    //     host: 'lop.med.exchange', // Bind to your domain
    //     port: 5173,               // Ensure it's an open port
    //     strictPort: true,
    //     hmr: {
    //       clientPort: 443,        // Required for Hot Module Replacement on Azure
    //     }
    //   }
});
