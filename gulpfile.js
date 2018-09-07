const gulp = require('gulp');
const { exec } = require('child_process');

// SASS FILE PATHS
const sassIn = 'src/scss/main.scss'
const sassOut = 'dist/css/bundle.css';

const requireDir = require('require-dir');
requireDir('public/src/commands');

gulp.task('watch', () => {

    gulp.watch(sassIn, ['sass']);
});

gulp.task('default', ['watch']);