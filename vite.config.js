import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
    server: {
        host: '0.0.0.0', // Permite que o Docker exponha a porta
        port: 5173,
        hmr: {
            host: 'localhost', // Diz pro Laravel buscar o JS no localhost do Windows
        },
        watch: {
            usePolling: true, // Força a atualização dos arquivos no WSL
        }
    }
});