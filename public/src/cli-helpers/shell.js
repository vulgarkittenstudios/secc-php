/* import exteral npde modules */ 
const { spawn } = require('child_process');

module.exports = class {
    static exec(...args) {
        let cmd = spawn(args[0], args[1]);
        
        cmd.stdout.on('data', (data) => {
          console.log('stdout: '+data.toString());
        });
        
        cmd.stderr.on('data', function (data) {
          console.log('stderr: ' + data.toString());
        });
        
        cmd.on('exit', function (code) {
          console.log('child process exited with code ' + code.toString());
        });

    }
}