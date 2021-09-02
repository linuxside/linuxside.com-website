'use strict';

/**
 * Minify all platform assets (CSS, Javascript and images) using Gulp.
 * Read about it @ https://www.npmjs.com/package/gulp
 */
const path = require('path')
const gulp = require('gulp')
const sass = require('gulp-sass')
const rename = require('gulp-rename')
const concat = require('gulp-concat')
const imagemin = require('gulp-imagemin')
const uglify = require('gulp-uglify')
const autoprefixer = require('gulp-autoprefixer')
const tap = require('gulp-tap')
const browserify = require('browserify')
const buffer = require('gulp-buffer')
const logger = require('gulplog')
const del = require('del')

const isProduction = ('production' === process.env.NODE_ENV)

// General cleanup before running any tasks
gulp.task('generalCleanup', function() {
    console.log("Running 'generalCleanup' task...")
    return del([
        './public/css',
        './public/js',
        './public/font',
        './public/img',
        './public/posts',
    ])
})

gulp.task('copyFonts', function() {
    console.log('Copying "Google Nunito Sans" fonts')

    const fonts = [
        './resources/public/google-nunito-sans/*.eot',
        './resources/public/google-nunito-sans/*.svg',
        './resources/public/google-nunito-sans/*.ttf',
        './resources/public/google-nunito-sans/*.woff',
        './resources/public/google-nunito-sans/*.woff2',
    ]
    return gulp.src(fonts)
        .pipe(gulp.dest('./public/font/google-nunito-sans'))
})

gulp.task('compileSass', function() {
    console.log('Compiling sass file')

    const sassFiles = [
        './resources/public/css/bootstrap.scss',
        './resources/public/google-nunito-sans/font.scss',
        './resources/public/css/style.scss',
    ]
    const sassOptions = {
        outputStyle: isProduction ? 'compressed' : 'expanded',
        includePaths: path.resolve(__dirname, 'node_modules')
    }
    return gulp.src(sassFiles)
        .pipe(concat('concat.scss'))
        .pipe(sass(sassOptions).on('error', sass.logError))
        .pipe(rename('style.css'))
        .pipe(autoprefixer())
        .pipe(gulp.dest('./public/css'))
})

gulp.task('minifyVendorJavascript', function() {
    console.log('Minifying vendor js files')

    const jsFiles = [
        './node_modules/jquery/dist/jquery.slim.js',
        // './node_modules/popper.js/dist/umd/popper-utils.js',
        // './node_modules/popper.js/dist/umd/popper.js',
        './node_modules/bootstrap/dist/js/bootstrap.js',
    ]
    // https://davidwalsh.name/compress-uglify
    // http://lisperator.net/uglifyjs/compress
    const uglifyOptions = {
        compress: {
            sequences: true,
            dead_code: true,
            conditionals: true,
            booleans: true,
            unused: true,
            if_return: true,
            join_vars: true,
            drop_console: true
        },
        mangle: true
    }
    return gulp.src(jsFiles)
        .pipe(concat('vendor.js'))
        .pipe(uglify(uglifyOptions))
        .pipe(gulp.dest('./public/js'))
})

gulp.task('minifyJavascript', function() {
    console.log('Minifying js files')

    // https://davidwalsh.name/compress-uglify
    // http://lisperator.net/uglifyjs/compress
    const uglifyOptions = {
        compress: {
            sequences: true,
            dead_code: true,
            conditionals: true,
            booleans: true,
            unused: true,
            if_return: true,
            join_vars: true,
            drop_console: true
        },
        mangle: true
    }
    return gulp.src('./resources/public/js/script.js')
        // https://github.com/gulpjs/gulp/blob/master/docs/recipes/browserify-multiple-destination.md
        .pipe(tap(function (file) {
            logger.info('bundling ' + file.path);
            // replace file contents with browserify's bundle stream
            file.contents = browserify(file.path, {debug: true}).bundle();
        }))
        // transform streaming contents into buffer contents (because gulp-uglify does not support streaming contents)
        .pipe(buffer())
        .pipe(uglify(uglifyOptions))
        .pipe(gulp.dest('./public/js'))
})

gulp.task('minimizeBlogImages', function() {
    console.log('Copying and optimizing blog images')

    const images = [
        './resources/public/img/**/*.png',
        './resources/public/img/**/*.svg',
        './resources/public/img/**/*.jpg',
    ]
    const imageMinSettings = [
        imagemin.gifsicle({interlaced: true}),
        imagemin.mozjpeg({quality: 80, progressive: true}),
        imagemin.optipng({optimizationLevel: 5}),
        imagemin.svgo({
            plugins: [
                {removeViewBox: true},
                {cleanupIDs: true}
            ]
        })
    ]
    return gulp.src(images)
        .pipe(imagemin(imageMinSettings))
        .pipe(gulp.dest('./public/img'))
})

gulp.task('minimizePostsImages', function() {
    console.log('Copying and optimizing posts images')

    const images = [
        './posts/public/**/*.png',
    ]
    const imageMinSettings = [
        imagemin.gifsicle({interlaced: true}),
        imagemin.mozjpeg({quality: 80, progressive: true}),
        imagemin.optipng({optimizationLevel: 5}),
        imagemin.svgo({
            plugins: [
                {removeViewBox: true},
                {cleanupIDs: true}
            ]
        })
    ]
    return gulp.src(images)
        .pipe(imagemin(imageMinSettings))
        .pipe(gulp.dest('./public/posts'))
})

// Main tasks (CSS & JS)
gulp.task('mainTasks', gulp.series(['copyFonts', 'compileSass', 'minifyVendorJavascript', 'minifyJavascript', 'minimizeBlogImages', 'minimizePostsImages']))

// Run ALL Gulp taks
gulp.task('default', gulp.series(['generalCleanup', 'mainTasks']))

// Individual tasks (under development environment)
// gulp.task('default', gulp.series(['compileSass']))
// gulp.task('default', gulp.series(['minifyJavascript']))
// gulp.task('default', gulp.series(['minimizeBlogImages']))
