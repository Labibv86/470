import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({

    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/explore.css',
                'resources/css/login.css',
                'resources/css/prefer.css',
                'resources/css/signinginfo.css',
                'resources/css/exploreout.css',
                'resources/css/resale.css',
                'resources/css/rental.css',
                'resources/css/cart.css',
                'resources/css/seller.css',
                'resources/css/ownershopsetup.css',
                'resources/css/ownerinterface.css',
                'resources/css/myaccount.css',
                'resources/css/sellingiteminfo.css'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: 'localhost',
        hmr: {
            host: 'localhost',
        },
    },
});
