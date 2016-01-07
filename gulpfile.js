var gulp = require('gulp');
var sass = require('gulp-sass');
var eslint = require('gulp-eslint');
var rename = require("gulp-rename");
var uglify = require('gulp-uglify');


gulp.task('style', function() {
    return gulp.src('./sass/racktemp.scss')
        .pipe(sass({
            outputStyle: 'compressed',
            includePaths: [
                './bower_components/bootstrap-sass/assets/stylesheets/bootstrap/',
            ]
        }).on('error', sass.logError))
        .pipe(rename('racktemp.min.css'))
        .pipe(gulp.dest('./web/css'));
});

gulp.task('copy', function() {
    gulp.src('./assets/footer_lodyas.png').pipe(gulp.dest('./web/img'));
    gulp.src('./bower_components/bootstrap-sass/assets/javascripts/bootstrap.min.js').pipe(gulp.dest('./web/js'));
    gulp.src('./bower_components/jquery/dist/jquery.min.js').pipe(gulp.dest('./web/js'));
    gulp.src('./bower_components/c3/c3.min.js').pipe(gulp.dest('./web/js'));
    gulp.src('./bower_components/d3/d3.min.js').pipe(gulp.dest('./web/js'));
    gulp.src('./bower_components/bootstrap-sass/assets/fonts/bootstrap/*').pipe(gulp.dest('./web/css'));
});

gulp.task('lint', function() {
    return gulp.src(['./js/RackTemp.js'])
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(eslint.failAfterError());
});

gulp.task('compress', ['lint'], function() {
    return gulp.src('js/RackTemp.js')
        .pipe(uglify())
        .pipe(rename('racktemp.min.js'))
        .pipe(gulp.dest('./web/js'));
});

gulp.task('default', ['style', 'copy', 'compress']);
