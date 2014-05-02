/*jshint unused:false */

function clock(){
  function checkTime(i){
    if (i < 10)
      i = "0" + i;
    return i;
  }

  var today = new Date();
  var h = today.getHours();
  var m = today.getMinutes();

  h = checkTime(h);
  m = checkTime(m);

  document.getElementById('clock').innerHTML = h + ":" + m;

  setTimeout(clock(),500);
}
