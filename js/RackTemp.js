/* jshint unused: false */
/* global d3, c3 */

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
          label: 'Hour',
          type: 'timeseries',
          tick: {
            count: 12,
            format: '%H'
          }
        },
        y:{
          label: {
            text: 'Degree (C)',
            position: 'outer-middle'
          }
        }
      }
    };

    var week = {
      bindto: '#week',
      data: _default.data,
      axis: {
        x: {
          type: 'timeseries',
          tick: {
            count: 12,
            format: '%d/%m %H'
          }
        },
        y:{
          label: {
            text: 'Degree (C)',
            position: 'outer-middle'
          }
        }
      }
    };

    // Format date object to HH:MM with leading zero
    var timeFormat = d3.time.format('%H:%M');

    return {
      hour: hour,
      today: today,
      week: week
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

  var _isArrayAllNull = function (arr){
    for (var i = arr.length - 1; i >= 0; i--) {
      if (arr[i] !== null)
        return false;
    }
    return true;
  };

  var isChartEmpty = function (chartArray){
    for (var i = 1; i < chartArray.length; i++) {
      if (!_isArrayAllNull(chartArray[i]))
        return false;
    }
    return true;
  };

  var _timestamp, _token, _host;

  var _makeApiUrl = function (apiPath){
    // TODO: Make protocol dynamic, maby send it with the vars from setApiInfo
    // using PHP check on HTTP_REFERER for https
    return 'http://'+_timestamp+':'+_token+'@'+_host+'/api/'+apiPath;
  };

  var setApiInfo = function(timestamp, token, host){
    _timestamp = timestamp;
    _token = token;
    _host = host;
  };

  var _fetchData = function (url, callback){
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState !== 4 || xhr.status !== 200){ return; }
      callback(xhr.responseText);
    };
    xhr.send();
  };

  var createChartToday = function(){
    var chartToday = c3.generate(chart.today);

    _fetchData(_makeApiUrl('graph/span/day'), function(resp){
      var chartData = JSON.parse(resp);
      if (RackTemp.isChartEmpty(chartData)) {
        document.getElementById(chart.today.bindto.substring(1)).innerHTML = 'No chart data';
      }else{
        chartToday.load({
          columns: chartData
        });
      }
    });
  };

  var createChartHour = function(){
    var config = chart.hour;
    var chartHour = c3.generate(config);

    _fetchData(_makeApiUrl('graph/span/hour'), function(resp){
      var chartData = JSON.parse(resp);
      if (RackTemp.isChartEmpty(chartData)) {
        document.getElementById(config.bindto.substring(1)).innerHTML = 'No chart data';
      }else{
        chartHour.load({
          columns: chartData
        });
      }
    });
  };

  var createChartWeek = function(){
    var config = chart.week;
    var chartWeek = c3.generate(config);

    _fetchData(_makeApiUrl('graph/span/week'), function(resp){
      var chartData = JSON.parse(resp);
      if (RackTemp.isChartEmpty(chartData)) {
        document.getElementById(config.bindto.substring(1)).innerHTML = 'No chart data';
      }else{
        chartWeek.load({
          columns: chartData
        });
      }
    });
  };

  return {
    chart: chart,
    clock: clock,
    loadToTab: loadToTab,
    isChartEmpty: isChartEmpty,
    setApiInfo: setApiInfo,
    createChartToday: createChartToday,
    createChartHour: createChartHour,
    createChartWeek: createChartWeek
  };

})();
