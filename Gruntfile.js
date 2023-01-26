module.exports = function( grunt ) {

	// load all grunt tasks in package.json matching the `grunt-*` pattern
	require( 'load-grunt-tasks' )( grunt );

	// Project configuration
	grunt.initConfig( {
		pkg:    grunt.file.readJSON( 'package.json' ),
		makepot: {
            target: {
                options: {
                	exclude: [
	                    'assets/.*', 'images/.*', 'node_modules/.*', 'tests/.*', 'release/.*', 'build/.*'
	                ],
                    domainPath: '/languages',
                    mainFile: 'wpclubmanager.php',
                    potFilename: 'wp-club-manager.pot',
                    potHeaders: {
                        poedit: true,                 // Includes common Poedit headers.
                        'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
                    },
                    type: 'wp-plugin'
                }
            }
        },
		watch:  {

		},
		clean: {
			main: ['release'],
			build: ['release/<%= pkg.version %>/build', 'release/<%= pkg.version %>/vendor/nikic/fast-route/test', 'release/<%= pkg.version %>/vendor/typisttech/imposter', 'release/<%= pkg.version %>/vendor/typisttech/imposter-plugin']
		},
		copy: {
			// Copy the plugin to a versioned release directory
			main: {
				src:  [
					'**',
					'!vendor/**',
					'!node_modules/**',
					'!tests/**',
					'!release/**',
					'!.git/**',
					'!.sass-cache/**',
					'!css/src/**',
					'!js/src/**',
					'!img/src/**',
					'!Gruntfile.js',
					'!package.json',
					'!.gitignore',
					'!.gitmodules',
					'!.github',
					'!phpcs.xml.dist',
					'!README.md',
					'!yarn.lock',
					'!phpstan.neon.dist'
				],
				dest: 'release/<%= pkg.version %>/'
			}
		},
		compress: {
			main: {
				options: {
					mode: 'zip',
					archive: './release/wp-club-manager.<%= pkg.version %>.zip'
				},
				expand: true,
				cwd: 'release/<%= pkg.version %>/',
				src: ['**/*'],
				dest: 'wp-club-manager/'
			}
		}
	} );

	grunt.loadNpmTasks('git-changelog');

	// Default task.
	grunt.registerTask( 'css', [ 'sass', 'cssmin'] );
	grunt.registerTask( 'js', ['concat', 'uglify'] );
	grunt.registerTask( 'default', ['js', 'css'] );
	grunt.registerTask( 'do_pot', ['makepot'] );

	grunt.registerTask( 'build', ['clean:main', 'copy', 'clean:build', 'compress'] );

	grunt.util.linefeed = '\n';
};
