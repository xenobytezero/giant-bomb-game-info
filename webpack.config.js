const path = require('path');

const ExtractTextPlugin = require('extract-text-webpack-plugin');

let extractTextPlugin_Style = new ExtractTextPlugin('[name]/style.css');
let extractTextPlugin_EditorStyle = new ExtractTextPlugin('[name]/style-editor.css');


module.exports = {

	entry: {
		'block': path.resolve(__dirname, 'js/gutenberg/block.js'),
		'options': path.resolve(__dirname, 'js/options.js')
    },
	output: {
        filename: '[name].dist.js',
        path: path.resolve(__dirname, 'dist/'),
    },
	module: {
		rules: [
			{
				test: /\.js$/,
				loader: 'babel-loader',
                exclude: /node_modules/
			},{
				test: /style\.scss$/,
				use: extractTextPlugin_Style.extract(
					['css-loader', 'sass-loader'] 
                )
			},{
				test: /style-editor\.scss$/,
				use: extractTextPlugin_EditorStyle.extract(
					['css-loader', 'sass-loader'] 
                )
			}
			
		],
	},
	plugins: [extractTextPlugin_Style, extractTextPlugin_EditorStyle],
};