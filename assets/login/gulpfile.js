var gulp = require( 'gulp' ),
	compass = require( 'gulp-compass' );

gulp.task( 'compass', function(){
	return gulp.src( 'sass/*.sass' ).pipe( compass( {
		css     : 'css',
		sass    : 'sass',
		image   : 'images',
		style   : 'compressed',
		relative_assets: true
	} ) )
	.pipe( gulp.dest( 'css' ) )
});

gulp.task( 'watch', [ 'compass' ], function(){
	gulp.watch( 'sass/**/*.sass', [ 'compass' ] );
} );

gulp.task( 'default', [ 'watch' ] );