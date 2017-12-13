/*!
 *    PASSY - Modern HTML5 Password Manager
 *    Copyright (C) 2017 Sefa Eyeoglu <contact@scrumplex.net> (https://scrumplex.net)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

const gulp = require('gulp');
const pump = require('pump');

// CSS
const postcss = require('gulp-postcss');
const sourcemaps = require('gulp-sourcemaps');
const sass = require('gulp-sass');

// JS
const uglify = require('gulp-uglify');
const babel = require('gulp-babel');

gulp.task('css-prod', function (cb) {
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

gulp.task('js-prod', function (cb) {
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

gulp.task('css-dev', function (cb) {
	pump([
			gulp.src('assets/src/scss/application.scss'),
			sourcemaps.init(),
			sass(),
			sourcemaps.write(),
			gulp.dest('assets/css')
		],
		cb);

});

gulp.task('js-dev', function (cb) {
	pump([
			gulp.src('assets/src/js/*.js'),
			sourcemaps.init(),
			sourcemaps.write(),
			gulp.dest('assets/js')
		],
		cb);

});

gulp.task('default', ['css-prod', 'js-prod']);
