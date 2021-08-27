'use strict';

const { VueLoaderPlugin } = require('vue-loader');
module.exports = {
    resolve: {
        alias: {
            vue: '@vue/compat'
        }
    },
    mode: 'development', entry: ['./src/app.js'],
    output: { path: __dirname + '/../public/ui/js/dist', publicPath: '/ui/js/dist/', filename: 'main.js', chunkFilename: '[name]-chunk.js' },
    module: {
        rules: [{ test: /\.vue$/, loader: 'vue-loader',
        options: {
            compilerOptions: {
                compatConfig: {
                    MODE: 2
                }
            }
        } }        
        ]
    }, plugins: [new VueLoaderPlugin()]
};