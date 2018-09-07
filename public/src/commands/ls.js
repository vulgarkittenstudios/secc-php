/* import exteral npde modules */ 
const { exec } = require('child_process');
const   gulp   = require('gulp');
const    fs    = require('fs');

class ls {
    static commands(...args) {
      getDir('commands');
    }
    
    static controllers(...args) {
        getDir('controllers');
    }
    
    static accessors(...args) {
        getDir('models/accessors');
    }
    
    static filters(...args) {
        getDir('models/filters');
    }
    
    static services(...args) {
        getDir('models/services');
    }
}

const getDir = (dir) => {
    fs.readdir('./src/'+dir, (err, files) => {
      
          for(let i = 0; i < files.length; i++) {
              let cmd = files[i].split('.');
              console.log(cmd[0]);
          }
      });
}


gulp.task('ls', () => {
    
    /* Itterate all extra input please */
    for(let i = 0; i < process.argv.length; i++) {
        
        
        let fn   = (process.argv[i]) ? process.argv[i].substring(2) : '';
        let argv = (process.argv[i+1] != undefined) ? process.argv[i+1] : '';
        let args = (argv.indexOf(':') != -1) ? argv.split(':') : argv;
        
        if (typeof ls[fn] == 'function')
            (typeof args == 'string') ? ls[fn](args) : ls[fn].apply(this, args);
    };
});