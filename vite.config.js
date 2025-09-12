import { resolve } from 'path';
// load polyfill for Node < 18 (use require to run in Node before esbuild)
try {
    // eslint-disable-next-line @typescript-eslint/no-var-requires
    require(resolve(process.cwd(), 'vite-crypto-polyfill.cjs'));
} catch (e) {
    // ignore
}
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
});
