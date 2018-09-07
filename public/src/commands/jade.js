/* import exteral npde modules */ 
const { exec } = require('child_process');
const   gulp   = require('gulp');
const    fs    = require('fs');
const   str    = require('../cli-helpers/str');
const recurse  = require('recursive-readdir');

class jade {
    static compile(file, callback) {
        return new Promise(() => {
            exec('jade '+file+' -P');
        }).then(callback());
    }
    
    static scan(dir) {
        recurse('./src/'+dir, (err, files) => {
            for(let i = 0; i < files.length; i++) {
                let ext = str.getExt(files[i]);
                
                if(ext == 'jade') {
                    jade.compile('./src/'+dir+'/'+files[i], () => {
                        console.log('compiling '+files[i]+'...');
                    });
                }
            }
        });
    }
}

gulp.task('jade', () => {
    
    /* Itterate all extra input please */
    for(let i = 0; i < process.argv.length; i++) {
        
        
        let fn   = (process.argv[i]) ? process.argv[i].substring(2) : '';
        let argv = (process.argv[i+1] != undefined) ? process.argv[i+1] : '';
        let args = (argv.indexOf(':') != -1) ? argv.split(':') : argv;
        
        if (typeof jade[fn] == 'function')
            (typeof args == 'string') ? jade[fn](args) : jade[fn].apply(this, args);
    };
});