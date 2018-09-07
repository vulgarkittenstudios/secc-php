/* import exteral npde modules */ 
const { exec } = require('child_process');
const   gulp   = require('gulp');
const    fs    = require('fs');
const    $     = require('jquery');

/* import internal npde modules */ 
const Template = require('../cli-helpers/template');



/**
 * This gulp task file allows
 * the user to generate common
 * components that are repetitive
*/

class make {
    
    static info(...args) {
        let str = '\n'; // console output
        
        // Itterate the current arguments 
        for(let i = 0; i < args.length; i++) {
            
            // Display info based on the user input
            switch(args[i]) {
                
                /* info it's selve's info */
                case '':
                str += 'This command allows you to \n'
                    +  'query information for a chosen \n'
                    +  'command and it\'s available options \n';       
                break;
                
                /* command generator info */ 
                case 'command':
                
                str += 'This action will generate a fresh \n'
                    +  'javascript file in the src/commands \n'
                    +  'directory with some boiler plate gulp \n';
                break;
                                
                /* model generator info */ 
                case 'model':
                
                str += 'This action will generate a fresh \n'
                    +  'javascript file in 1 of the src/models \n'
                    +  'directory with some boiler plate gulp defaults \n';
                break;
            }
            console.log(str);
        }
    }
    
    static command(...args) {
        
        create('command', args);
    }
    
    static accessor(...args) {
        create('models/accessor', args);
    }
    
    static filter(...args) {
        create('models/filter', args);
    }
    
    static service(...args) {
        create('models/service', args);
    }
    
    static view(...args) {

        create('view', args, '.jade');
    }
}

const create = (type, args, ext) => {
    let template = new Template('./src/meta/views/'+type, 's/'+type+'.js');
        
    let str = '\n'; // console output
    for(let i = 0; i < args.length; i++) {
        const name = args[i];
        if(typeof name != 'string') {
            str += 'Please specify a name \n'
                +  'for your new '+type+'!';
   
        } else {
            
            const data = template.compile({name: name});
            fs.writeFileSync('./src/'+type+'s/'+name+((!ext) ? '.js' : ext), data);
	               console.log('Your command file has been generated.');
        }
    }
    console.log(str);
}

gulp.task('make', () => {
    
    /* Itterate all extra input please */
    for(let i = 0; i < process.argv.length; i++) {
        
        
        let fn   = (process.argv[i]) ? process.argv[i].substring(2) : '';
        let argv = (process.argv[i+1] != undefined) ? process.argv[i+1] : '';
        let args = (argv.indexOf(':') != -1) ? argv.split(':') : argv;
        
        if (typeof make[fn] == 'function')
            (typeof args == 'string') ? make[fn](args) : make[fn].apply(this, args);
    };
});