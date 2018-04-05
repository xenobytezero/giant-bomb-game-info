const path = require('path');

const ExtractTextPlugin = require('extract-text-webpack-plugin');

let extractTextPlugin_Style = new ExtractTextPlugin('[name]/style.css');
let extractTextPlugin_EditorStyle = new ExtractTextPlugin('[name]/style-editor.css');


module.exports = {

	entry: {
		//'plugin': path.resolve(__dirname, 'js/gutenberg/plugin.js')
		'block': path.resolve(__dirname, 'js/gutenberg/block.js')
    },
	output: {
        filename: '[name].js',
        path: path.resolve(__dirname, 'dist/gutenberg/'),
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