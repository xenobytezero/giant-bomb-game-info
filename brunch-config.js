module.exports = {

    // Plugins ----------------------------------------------------------------------

    plugins: {

        sass: {
            mode: 'native',
            debug: 'comments', // or set to 'debug' for the FireSass-style output
            options: {
                importer: [
                    'node-sass-import-once'
                ]
            }
        },

        // --------------------------------------------------------------------------

    },

    modules: {
        autoRequire: {
            'blocks/gbgi-block/block.js': ['blocks/gbgi-block/block.js'],
            'js/options.js': ['js/options.js']
        }
    },

    // ------------------------------------------------------------------------

    conventions: {
        ignored: [
            // don't compile these things, but watch them
            //'js/**/*'
        ]
    },

    // ------------------------------------------------------------------------

    paths: {
        public: './dist',
        watched: [
            'js',
            'blocks'
        ]
    },

    // ------------------------------------------------------------------------

    files: {
        javascripts: {
            entryPoints: {
                'blocks/gbgi-block/block.js': 'blocks/gbgi-block/block.js',
                'js/options.js': 'js/options.js'
            }
        },
        stylesheets: {
            joinTo: {
                'blocks/gbgi-block/style.css': 'blocks/gbgi-block/style.scss',
                'blocks/gbgi-block/style-editor.css': 'blocks/gbgi-block/style-editor.scss'       
            }
        }
    }

}