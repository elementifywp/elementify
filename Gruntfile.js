module.exports = function( grunt ) {
	'use strict';

	grunt.initConfig( {
		pkg: grunt.file.readJSON( 'package.json' ),

		copy: {
			main: {
				options: {
					mode: true,
				},
				src: [
					'**',
					'!.git/**',
					'!.github/**',
					'!.grunt/**',
					'!node_modules/**',
					'!vendor/**',
					'!bin/**',
					'!tests/**',
					'!languages/**/*.po',
					/**
					 * Source files – only the compiled output in assets/build ships.
					 */
					'!assets/src/**',
					/**
					 * Build tooling and config – not needed in the released theme.
					 */
					'!Gruntfile.js',
					'!postcss.config.js',
					'!webpack.config.js',
					'!tailwind.config.js',
					'!package.json',
					'!package-lock.json',
					'!yarn.lock',
					'!pnpm-lock.yaml',
					'!pnpm-workspace.yaml',
					'!composer.json',
					'!composer.lock',
					'!phpcs.xml',
					'!phpcs.xml.dist',
					'!phpunit.xml',
					'!phpunit.xml.dist',
					'!.editorconfig',
					'!.babelrc',
					'!.nvmrc',
					'!.eslintrc.json',
					'!.eslintignore',
					'!.stylelintrc',
					'!.stylelintignore',
					'!.lintstagedrc.js',
					'!.gitignore',
					'!.claude/**',
					'!CONTRIBUTING.md',
					'!README.md',
					'!*.sh',
					'!*.map',
					'!*.zip',
					'!elementify/**',
				],
				dest: 'elementify/',
			},
		},

		compress: {
			main: {
				options: {
					archive: 'elementify.zip',
					mode: 'zip',
				},
				files: [
					{
						src: [ './elementify/**' ],
					},
				],
			},
		},

		clean: {
			main: [ 'elementify' ],
			zip: [ 'elementify.zip' ],
		},

		makepot: {
			target: {
				options: {
					domainPath: '/languages',
					mainFile: 'style.css',
					potFilename: 'elementify.pot',
					potHeaders: {
						poedit: true,
						'x-poedit-keywordslist': true,
						'pot-creation-date': new Date().toISOString(),
						'language-team': 'Elementify Themes <support@elementifythemes.com>',
						'report-msgid-bugs-to': 'https://github.com/elementifywp/elementify/issues',
					},
					type: 'wp-theme',
					updateTimestamp: true,
				},
			},
		},

		wp_readme_to_markdown: {
			your_target: {
				files: {
					'README.md': 'readme.txt',
				},
			},
		},

		addtextdomain: {
			options: {
				textdomain: 'elementify',
			},
			target: {
				files: {
					src: [
						'*.php',
						'**/*.php',
						'!node_modules/**',
						'!vendor/**',
						'!tests/**',
						'!bin/**',
					],
				},
			},
		},

		/**
		 * Check textdomain
		 */
		checktextdomain: {
			standard: {
				options: {
					text_domain: 'elementify', //Specify allowed domain(s)
					keywords: [
						//List keyword specifications
						'__:1,2d',
						'_e:1,2d',
						'_x:1,2c,3d',
						'esc_html__:1,2d',
						'esc_html_e:1,2d',
						'esc_html_x:1,2c,3d',
						'esc_attr__:1,2d',
						'esc_attr_e:1,2d',
						'esc_attr_x:1,2c,3d',
						'_ex:1,2c,3d',
						'_n:1,2,4d',
						'_nx:1,2,4c,5d',
						'_n_noop:1,2,3d',
						'_nx_noop:1,2,3c,4d',
					],
				},
				files: [
					{
						src: [
							'**/*.php', //all php
							'!node_modules/**',
							'!vendor/**',
							'!tests/**',
						],
						expand: true,
					},
				],
			},
		},
	} );

	/**
	 * Load Grunt Tasks
	 */
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-compress' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );

	/* Read File Generation task */
	grunt.loadNpmTasks( 'grunt-wp-readme-to-markdown' );

	// Generate Read me file
	grunt.registerTask( 'readme', [ 'wp_readme_to_markdown' ] );

	// i18n
	grunt.registerTask( 'i18n', [ 'checktextdomain', 'addtextdomain', 'makepot' ] );

	// Generate Release package
	grunt.registerTask( 'release', [
		'clean:zip',
		'copy',
		'compress',
		'clean:main',
	] );

	grunt.util.linefeed = '\n';
};
