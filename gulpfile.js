var gulp = require('gulp');
var postcss = require('gulp-postcss');
var sourcemaps = require('gulp-sourcemaps');
var sass = require('gulp-sass');

gulp.task('css', function () {
	return gulp.src('./assets/scss/application.scss')
		.pipe(sourcemaps.init())
		.pipe(postcss([
			require('autoprefixer'),
			require('postcss-csso')
		], {
			syntax: require('postcss-scss')
		}))
		.pipe(sass.sync({outputStyle: 'compressed'}).on('error', sass.logError))
		.pipe(sourcemaps.write())
		.pipe(gulp.dest('./assets/css'));
});

gulp.task('default', ['css']);
