const CopyWebpackPlugin = require('copy-webpack-plugin');

module.exports = {
    devServer: {
        proxy: 'http://localhost/century/'
    },
    configureWebpack: {
        performance: {
            maxAssetSize: 500000,
            maxEntrypointSize: 400000,
        },
        plugins: [
            new CopyWebpackPlugin([{ from: 'api', to: 'api' }])
        ],
    }
}