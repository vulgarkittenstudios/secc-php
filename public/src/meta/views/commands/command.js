/* import exteral npde modules */ 
const { exec } = require('child_process');
const   gulp   = require('gulp');
const    fs    = require('fs');

class {{name}} {
    
}


gulp.task('{{name}}', () => {
    
    /* Itterate all extra input please */
    for(let i = 0; i < process.argv.length; i++) {
        
        
        let fn   = (process.argv[i]) ? process.argv[i].substring(2) : '';
        let argv = (process.argv[i+1] != undefined) ? process.argv[i+1] : '';
        let args = (argv.indexOf(':') != -1) ? argv.split(':') : argv;
        
        if (typeof {{name}}[fn] == 'function')
            (typeof args == 'string') ? {{name}}[fn](args) : {{name}}[fn].apply(this, args);
    };
});