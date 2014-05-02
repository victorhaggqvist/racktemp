/*! racktemp - v0.0.1 - 2014-05-02
* Copyright (c) 2014 ; Licensed  */
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

//Javascript to enable linking to tabs
var url = document.location.toString();
if (url.match('#')) {
  $('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
}

// Change hash for page-reload
$('.nav-tabs a').on('shown', function (e) {
  window.location.hash = e.target.hash;
});
