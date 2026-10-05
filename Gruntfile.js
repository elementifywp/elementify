/**
 * Grunt tasks for packaging the Elementify theme.
 *
 * Usage:
 *   pnpm run release        Build assets, check i18n/versions, create the ZIP.
 *   pnpm exec grunt release Same, but skips the webpack build.
 *
 * Output: dist/elementify-<version>.zip containing a single `elementify/`
 * folder, ready to upload via Appearance → Themes → Add New → Upload.
 *
 * @param {Object} grunt Grunt instance.
 */
module.exports = function ( grunt ) {
	require( 'load-grunt-tasks' )( grunt );

	const pkg = grunt.file.readJSON( 'package.json' );
	const slug = pkg.name;

	// Allowlist of files that ship with the theme. Anything not listed here
	// (sources, tests, tooling configs, vendor/, node_modules/) stays out.
	const themeFiles = [
		'*.php',
		'style.css',
		'rtl.css',
		'theme.json',
		'screenshot.{png,jpg,jpeg,webp}',
		'readme.txt',
		'LICENSE*',
		'wpml-config.xml',
		'assets/build/**',
		'inc/**',
		'languages/**',
		'template-parts/**',
		// Block theme folders, picked up automatically if added later.
		'patterns/**',
		'parts/**',
		'templates/**',
		'styles/**',
		// Never ship these, even inside allowed folders.
		'!**/*.map',
		'!**/.DS_Store',
		'!**/*.tmp',
	];

	// Files WordPress requires (or the theme cannot run without).
	const requiredFiles = [
		'style.css',
		'index.php',
		'functions.php',
		'screenshot.png',
		'readme.txt',
		'assets/build/css/main.css',
		'assets/build/js/main.js',
	];

	grunt.initConfig( {
		pkg,

		clean: {
			temp: {
				src: [
					'**/*.tmp',
					'**/.afpDeleted*',
					'**/.DS_Store',
					'!node_modules/**',
					'!vendor/**',
				],
				dot: true,
				filter: 'isFile',
			},
			dist: [ 'dist/' ],
		},

		// Every translatable string must use the theme's text domain.
		checktextdomain: {
			options: {
				text_domain: slug,
				report_missing: true,
				keywords: [
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
			files: {
				src: [ '*.php', 'inc/**/*.php', 'template-parts/**/*.php' ],
				expand: true,
			},
		},

		copy: {
			theme: {
				files: [
					{
						expand: true,
						src: themeFiles,
						dest: `dist/${ slug }/`,
					},
				],
			},
		},

		compress: {
			theme: {
				options: {
					mode: 'zip',
					archive: `dist/${ slug }-${ pkg.version }.zip`,
				},
				expand: true,
				cwd: `dist/${ slug }/`,
				src: [ '**/*' ],
				dest: `${ slug }/`,
			},
		},
	} );

	// Version must match in package.json, style.css and readme.txt.
	// (ELEMENTIFY_VERSION is read from style.css at runtime, so it follows.)
	grunt.registerTask(
		'version-check',
		'Verify version strings are in sync.',
		function () {
			const sources = [
				[ 'style.css', /^\s*Version:\s*(\S+)/m ],
				[ 'readme.txt', /^\s*Stable tag:\s*(\S+)/m ],
			];

			let ok = true;
			sources.forEach( ( [ file, pattern ] ) => {
				const match = grunt.file.read( file ).match( pattern );
				const found = match ? match[ 1 ] : null;
				if ( found === pkg.version ) {
					grunt.log.ok( `${ file }: ${ found }` );
				} else {
					ok = false;
					grunt.log.error(
						`${ file }: found "${ found }", expected "${ pkg.version }" (package.json)`
					);
				}
			} );

			if ( ! ok ) {
				grunt.fail.warn( 'Version strings are out of sync.' );
			}
		}
	);

	// Fail early if a required theme file or the compiled assets are missing.
	grunt.registerTask(
		'theme-check',
		'Verify required theme files exist.',
		function () {
			const missing = requiredFiles.filter(
				( file ) => ! grunt.file.exists( file )
			);

			if ( missing.length ) {
				missing.forEach( ( file ) =>
					grunt.log.error( `Missing: ${ file }` )
				);
				grunt.fail.warn(
					'Required files are missing. Run `pnpm run build` first?'
				);
			}

			grunt.log.ok( 'All required theme files present.' );
		}
	);

	grunt.registerTask( 'finish', function () {
		grunt.log.writeln( '----------' );
		grunt.log.ok( `ZIP created: dist/${ slug }-${ pkg.version }.zip` );
	} );

	grunt.registerTask( 'check', [
		'version-check',
		'theme-check',
		'checktextdomain',
	] );
	grunt.registerTask( 'build', [ 'copy:theme', 'compress:theme', 'finish' ] );
	grunt.registerTask( 'release', [ 'clean', 'check', 'build' ] );
	grunt.registerTask( 'default', [ 'release' ] );
};
