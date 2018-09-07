/* import exteral npde modules */ 
const { exec } = require('child_process');
const   gulp   = require('gulp');
const    fs    = require('fs');

class rm {
    static command(...args) {
      
        del('command', args[0]);
    }
    
    static controller(...args) {
        del('controller', args[0]);
    }
    
    static view(...args) {
        del('view', args[0]);
    }
    
    static meta(...args) {
        let str = "";
        // Itterate the current arguments 
        for(let i = 0; i < args.length; i++) {
            
            // Display info based on the user input
            switch(args[i]) {
                
                /* info it's selve's info */
                case '':
                str += 'This command allows you to \n'
                    +  'remove elements from a chosen type \n'
           
                break;
                
                case 'view':
                
                break;
            }
        }
    }
}

const del = (type, file) => {
    fs.readdir('./src/'+type+'s', (err, files) => {
      
          for(let i = 0; i < files.length; i++) {
              let cmd = files[i].split('.');
              
              if(cmd[0] == file) {
              
                  fs.unlink('./src/'+type+'s/'+files[i], (err) => {
                      if(err) throw err;
                      else console.log(cmd[0]+' successfully deleted.');
                  });
              }
          }
      });
}

gulp.task('rm', () => {
    
    /* Itterate all extra input please */
    for(let i = 0; i < process.argv.length; i++) {
        
        
        let fn   = (process.argv[i]) ? process.argv[i].substring(2) : '';
        let argv = (process.argv[i+1] != undefined) ? process.argv[i+1] : '';
        let args = (argv.indexOf(':') != -1) ? argv.split(':') : argv;
        
        if (typeof rm[fn] == 'function')
            (typeof args == 'string') ? rm[fn](args) : rm[fn].apply(this, args);
    };
});