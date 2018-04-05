module.exports = function(grunt) {

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-sass');

    grunt.initConfig({

        watch: {

            sassMetabox: {
                files: ['sass/metabox.scss'],
                tasks: ['sass:metabox']
            }


        },


        sass: {
            metabox: {
                files: {
                    'css/metabox.css': ['sass/metabox.scss'] 
                }
            }

        
        }


    })


}