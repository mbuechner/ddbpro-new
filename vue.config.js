const CompressionPlugin = require('compression-webpack-plugin');
const path = require('path');

module.exports = {
    publicPath: 'themes/custom/ddbp/assets',
    outputDir: 'web/themes/custom/ddbp/assets',
    css: {
        extract: {
            filename: `css/[name].css`,
        },
    },
    chainWebpack: config => {
        config.output
            .filename(`js/[name].js`)
            .chunkFilename(`js/[name].js`);
            // Exclude html from building process since we don't need index.html
            config.plugins.delete('html');
            config.plugins.delete('preload');
            config.plugins.delete('prefetch');
            // Disable hot reload
            config.plugins.delete('hmr');
    },
    configureWebpack: {
        resolve: {
            alias: {
                "@": path.resolve(__dirname, 'web/themes/custom/ddbp/src')
            },
        },
        performance: {
            hints: process.env.NODE_ENV === 'development' ? false : 'warning',
        },
        entry: {
            app: './web/themes/custom/ddbp/src/main.js',
        },
        plugins: [
            new CompressionPlugin({
                compressionOptions: {
                    algorithm: 'gzip',
                }
            })
        ],
    },
    runtimeCompiler: true,
};
