#!/usr/bin/env node
var exec = require('child_process').exec;

runr();

function runr(){
  exec('php simulatetemp.php', function (error, stdout, stderr) {
    console.log('Temp Simulator Run '+new Date());
    if (error !== null) {
      console.log('exec error: ' + error);
    }
  });
  setTimeout(runr,1000*60*5); // every 5 min
}

// make kind exit of CTRL-C
process.on('SIGINT', function() {
  process.exit();
});
