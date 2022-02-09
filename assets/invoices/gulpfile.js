var gulp = require( 'gulp' ),
	compass = require( 'gulp-compass' );

gulp.task( 'compass', function(){
	return gulp.src( 'assets/sass/*.sass' ).pipe( compass( {
		css     : 'assets/css',
		sass    : 'assets/sass',
		image   : 'assets/images',
		style   : 'compressed',
		relative_assets: true
	} ) )
	.pipe( gulp.dest( 'assets/css' ) )
});

gulp.task( 'watch', [ 'compass' ], function(){
	gulp.watch( 'assets/sass/**/*.sass', [ 'compass' ] );
} );

gulp.task( 'default', [ 'watch' ] );