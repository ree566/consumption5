var gulp = require('gulp'),
	jshint = require('gulp-jshint'),
	uglify = require('gulp-uglify'),
	watch = require('gulp-watch');

gulp.task('uglify', function(){
	gulp.src('src/Please.js')
		.pipe(uglify({
			preserveComments: 'some',
		}))
		.pipe(gulp.dest('dist'))
});
gulp.task('lint', function(){
	return gulp.src('src/Please.js')
		.pipe(jshint())
		.pipe(jshint.reporter('jshint-stylish'));
});
/*
gulp.task('watch', function(){
	gulp.watch('Please.js',['uglify']);
});
*/
gulp.task('default', [], function(){
	gulp.start('uglify');
	gulp.start('lint');
});