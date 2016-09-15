// gulpfile.js 
var gulp = require('gulp'),
	concat = require('gulp-concat'),
    cleanCSS = require('gulp-clean-css');
    

gulp.task('css', function() {
    return gulp.src([
                    'staging/css/bootstrap.css',
                    'staging/css/animate.css',
                    'staging/css/font-awesome.min.css',
                    'staging/css/simple-sidebar.css',
                    'staging/css/slick.css',
                    'staging/css/freeze.css'
    ])
    	.pipe(concat('base-styles.min.css'))
        .pipe(cleanCSS())
        .pipe(gulp.dest('staging/css'));
});

// JS logic has problems after concatenation

// gulp.task('scripts', function() {
//     var js = gulp.src([
//         'staging/js/jquery-1.11.1.min.js',
//         'staging/js/bootstrap.min.js',
//         'staging/js/slick.min.js',
//         'staging/js/placeholdem.min.js',
//         'staging/js/rs-plugin/js/jquery.themepunch.plugins.min.js',
//         'staging/js/rs-plugin/js/jquery.themepunch.revolution.min.js',
//         'staging/js/waypoints.min.js',
//         'staging/js/moment.js',
//         'staging/js/datetimepicker.js',
//         'staging/js/datetimepicker.pair.js',
//         'staging/js/date.js',
//         'staging/js/scripts.js'
//     ])
//         .pipe(concat('base-scripts.min.js'))
//         // .pipe(uglify())
//         .pipe(gulp.dest('staging/js'));
// });

gulp.task('watch', function() {
    // gulp.watch('staging/js/*.js', ['scripts']);
    gulp.watch('staging/css/*.css', ['css']);
});

gulp.task('default', ['css']);