const path = require('path');
const util = require('util');

// ------------

const NodeSassImportOnce = require('node-sass-import-once');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

// ---------------------------------------

module.exports = {
    entry: {
        'blocks/gbgi-block/block': './blocks/gbgi-block/block.js',
        'blocks/gbgi-block/editor': './blocks/gbgi-block/editor.scss',
        'blocks/gbgi-block/style': './blocks/gbgi-block/style.scss',
        'plugin/plugin': './js/plugin.js'
    },

    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: '[name].build.js'
    },

    resolve: {
        modules: [
            'node_modules',
            path.resolve(__dirname)
        ]
    },

    plugins: [
        new MiniCssExtractPlugin({
            filename: "[id].css"
        })
    ],
    devtool: 'source-map',
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: [
                                '@babel/preset-env', 
                                ['@babel/preset-react', {
                                    pragma: "wp.element.createElement"
                                }]
                            ],
                    }
                }
            },    
            {
                test: /\.scss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            sourceMap: true
                        }
                        
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: true,
                            importer: NodeSassImportOnce
                        }
                    }
                ]
                  
            },
            {
                test: /\.(svg|eot|ttf|woff|woff2)/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[name].[ext]',
                            outputPath: 'assets/webfonts',
                            publicPath: '../assets/webfonts'
                        },
                    },
                ]
            }
        ]
    },


    externals: {
		wp: 'wp',
		ga: 'ga', // Old Google Analytics.
		gtag: 'gtag', // New Google Analytics.
        jquery: 'jQuery', // import $ from 'jquery' // Use the WordPress version after enqueuing it.
        react: {
            root: 'React',
            commonjs2: 'react',
            commonjs: 'react',
            amd: 'react'
        },
        'react-dom': {
            root: 'ReactDOM',
            commonjs2: 'react-dom',
            commonjs: 'react-dom',
            amd: 'react-dom'
        }
    }

};