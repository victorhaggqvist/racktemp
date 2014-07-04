/* jshint unused: false */
/* global d3 */

var RackTemp = (function (){
"use strict";

  // namespace for chart definitions for c3
  var chart = (function (){

    var _default = {
      data: {
        columns: [],
        type: 'spline',
        x: 'x'
      }
    };

    var hour = {
      bindto: '#today',
      data: _default.data,
      axis: {
        x: {
          type: 'timeseries',
          tick: {
            format: '%M'
          }
        }
      },
      tooltip: {
        format: {
          title: function (d) { return timeFormat(d); }
        }
      }
    };

    var today = {
      bindto: '#today',
      data: _default.data,
      axis: {
        x: {
          abel: 'Minute',
          type: 'timeseries',
          tick: {
            count: 12,
            format: '%H%M'
          }
        },
        y:{
          label: {
            text: 'Degree (C)',
            position: 'outer-middle'
          }
        }
      },
      tooltip: {
        format: {
          title: function (d) { return timeFormat(d); }
        }
      }
    };

    // Format date object to HH:MM with leading zero
    var timeFormat = d3.time.format('%H:%M');

    return {
      hour: hour,
      today: today
    };

  })();

  function clock (){

    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();

    h = _leadZero(h);
    m = _leadZero(m);

    document.getElementById('clock').innerHTML = h + ":" + m;

    // setTimeout(clock, 500); // sould not throw  Uncaught ReferenceError: RackTemp is not defined
  }

  function _leadZero(i){
    if (i < 10)
      i = "0" + i;
    return i;
  }

  // Enable linking to tabs
  var loadToTab = function (){
    var url = document.location.toString();
    if (url.match('#')) {
      $('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
    }

    // Change hash for page-reload
    $('.nav-tabs a').on('shown', function (e) {
      window.location.hash = e.target.hash;
    });
  };

  return {
    chart: chart,
    clock: clock,
    loadToTab: loadToTab
  };

})();
