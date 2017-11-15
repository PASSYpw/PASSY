const gulp = require('gulp');
const pump = require('pump');

// CSS
const postcss = require('gulp-postcss');
const sourcemaps = require('gulp-sourcemaps');
const sass = require('gulp-sass');

// JS
const uglify = require('gulp-uglify');
const babel = require('gulp-babel');

gulp.task('css', function (cb) {
	pump([
			gulp.src('assets/src/scss/application.scss'),
			sourcemaps.init(),
			postcss([
				require('autoprefixer'),
				require('postcss-csso')
			], {
				syntax: require('postcss-scss')
			}),
			sass({outputStyle: 'compressed'}),
			sourcemaps.write(),
			gulp.dest('assets/css')
		],
		cb);

});
gulp.task('js', function (cb) {
	pump([
			gulp.src('assets/src/js/*.js'),
			sourcemaps.init(),
			babel({
				presets: ['env']
			}),
			uglify(),
			sourcemaps.write(),
			gulp.dest('assets/js')
		],
		cb);

});

gulp.task('default', ['css', 'js']);
