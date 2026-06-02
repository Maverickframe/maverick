import { defineConfig, loadEnv } from "vite";
import liveReload from "vite-plugin-live-reload";
import svgSpritePlugin from "@pivanov/vite-plugin-svg-sprite";
import { resolve } from "path";

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), "");

    return {
        plugins: [
            liveReload(`${__dirname}/**/*.php`),
            svgSpritePlugin({
                iconDirs: [resolve(process.cwd(), "public/img/svg/sprite")],
                symbolId: "[name]",
                svgDomId: "sprite",
                inject: false,
            }),
        ],

        define: {
            global: "window",
        },
        root: "",
        base:
            process.env.NODE_ENV === "development" ? env.VITE_THEME_PATH : `./`,

        css: {
            preprocessorOptions: {
                scss: {
                    silenceDeprecations: [
                        "import",
                        "global-builtin",
                        "legacy-js-api",
                    ],
                },
            },
        },

        build: {
            outDir: resolve(__dirname, "build"),
            emptyOutDir: true,
            manifest: true,
            target: "es2018",

            rollupOptions: {
                input: {
                    main: resolve(__dirname, env.VITE_ENTRY_POINT),
                    css: resolve(__dirname, env.VITE_STYLES),
                    style: resolve(__dirname, env.VITE_STYLES_NEW),
                    blocks: resolve(__dirname, env.VITE_STYLES_BLOCKS),
                },
            },

            minify: true,
            write: true,
        },

        server: {
            open: env.VITE_LOCAL_SERVER,
            cors: {
                origin: "*",
            },
            strictPort: true,
            port: env.VITE_ASSETS_PORT,
            https: false,
            hmr: {
                host: "localhost",
            },
        },
    };
});
