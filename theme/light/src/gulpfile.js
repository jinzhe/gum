const { watch, src, dest, series } = require('gulp');
const less = require('gulp-less');
const autoprefixer = require('gulp-autoprefixer');
const minifycss = require('gulp-minify-css');
// var rename = require('gulp-rename');//重命名
// var uglify = require('gulp-uglify');//js压缩
// var pngquant = require('imagemin-pngquant'); //png图片压缩插件
function css(cb) {
	src('*.less')
		.pipe(less())
		.pipe(
			autoprefixer({
				browsers: [ 'last 2 versions', 'Android >= 4.0' ]
			})
		)
		.pipe(minifycss())
		.pipe(dest('../static/'));
	cb();
}

watch('*.less', series(css));

exports.default = series(css);
