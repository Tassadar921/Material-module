import { defineConfig } from "vite";
import symfonyPlugin from "vite-plugin-symfony";

export default defineConfig({
    plugins: [
        symfonyPlugin(),
    ],

    build: {
        rollupOptions: {
            input: {
                app: "./assets/app.js",
                material_edit: "./assets/javascript/material_edit.js",
            },
        }
    },
});
