export default (ctx) => ({
    plugins: {
        autoprefixer: {},
        cssnano: ctx.env === "production" ? {} : false,
    },
});
