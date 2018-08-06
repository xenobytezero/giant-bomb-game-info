module.exports = function(grunt) {



    let deployDest = 'deploy/<%= pkg.version %>/giant-bomb-game-info/';

    // Load the plugin that provides the "uglify" task.
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-bump');
    grunt.loadNpmTasks('grunt-replace');

    // Project configuration.
    grunt.initConfig({

        pkg: grunt.file.readJSON('package.json'),

        copy: {
        
            deploy: {
                files: [

                    // PHP
                    {src: ['index.php'], dest: deployDest},
                    {src: ['src/**/*.php'], dest: deployDest},

                    // Composer
                    {src: ['composer.json', 'composer.lock'], dest: deployDest},

                    // JS
                    {src: ['dist/js/**/*.js'], dest: deployDest},

                    // CSS
                    {src: ['dist/css/**/*.css'], dest: deployDest},

                    // Templates
                    {src: ['templates/**/*.twig'], dest: deployDest},

                    // Other
                    {
                        src: [
                            'README.md',
                            'LICENCE'
                        ], 
                        dest: deployDest
                    }
                ]
            }
        },

        bump: {
            options: {
                push:false
            }
        },

        replace: {
            deployedVersionTag: {

                src: [deployDest + '/index.php'],
                dest: deployDest + '/index.php',

                options: {
                    patterns: [{
                        match: 'releaseVersion',
                        replacement: '<%= pkg.version %>'
                    }]
                }
            }
        }


    });

    // Default task(s).
    grunt.registerTask('release', ['bump', 'copy:deploy', 'replace:deployedVersionTag']);
  
  };