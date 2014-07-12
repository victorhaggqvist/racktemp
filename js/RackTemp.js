/* jshint unused: false, devel: true */
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
          title: function (d) { return hourTooltipFormat(d); }
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

    var month = {
      bindto: '#month',
      data: _default.data,
      axis: {
        x: {
          type: 'timeseries',
          tick: {
            count: 24,
            format: '%d/%m'
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
          title: function (d) { return monthTooltipFormat(d); }
        }
      }
    };

    // Format date object to HH:MM with leading zero
    var hourTooltipFormat = d3.time.format('%H:%M');

    var monthTooltipFormat = d3.time.format('%d/%m %H');

    return {
      hour: hour,
      today: today,
      week: week,
      month: month
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

  var _noChartData = '<div class="jumbotron"><p>No chart data jet</p></div>';

  var createChartToday = function(){
    var chartToday = c3.generate(chart.today);

    _fetchData(_makeApiUrl('graph/span/day'), function(resp){
      var chartData = JSON.parse(resp);
      if (RackTemp.isChartEmpty(chartData)) {
        document.getElementById(chart.today.bindto.substring(1)).innerHTML = _noChartData;
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
        document.getElementById(config.bindto.substring(1)).innerHTML = _noChartData;
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
        document.getElementById(config.bindto.substring(1)).innerHTML = _noChartData;
      }else{
        chartWeek.load({
          columns: chartData
        });
      }
    });
  };

  var createChartMonth = function(){
    var config = chart.month;
    var chartMonth = c3.generate(config);

    _fetchData(_makeApiUrl('graph/span/month'), function(resp){
      var chartData = JSON.parse(resp);
      if (RackTemp.isChartEmpty(chartData)) {
        document.getElementById(config.bindto.substring(1)).innerHTML = _noChartData;
      }else{
        chartMonth.load({
          columns: chartData
        });
      }
    });
  };

  var _smtpPresetsSet = [{
    "host": "smtp.gmail.com",
    "port": "465",
    "encryption": 1 // TLS
  }];

  var smtpSettingsSetup = function(){
     var _smtpPresets = document.getElementById('smtp-presets');
     var _smtpHost = document.getElementById('smtp-host');
     var _smtpPort = document.getElementById('smtp-port');
     var _smtpEncryption = document.getElementById('smtp-encryption');
     var _smtpAuth = document.getElementById('smtp-auth');
     var _smtpUser = document.getElementById('smtp-user');
     var _smtpPassword = document.getElementById('smtp-password');

     _smtpPresets.onchange = function(e){
        var _currentPreset = _smtpPresets.options[_smtpPresets.selectedIndex].value;

        if (_smtpPresetsSet[_currentPreset] !== undefined) {
          _smtpHost.value = _smtpPresetsSet[_currentPreset].host;
          _smtpPort.value = _smtpPresetsSet[_currentPreset].port;
          _smtpEncryption.selectedIndex = _smtpPresetsSet[_currentPreset].encryption;
        }
     };

     _smtpAuth.onclick = function(){
      if (!_smtpAuth.checked) {
        _smtpUser.disabled = true;
        _smtpPassword.disabled = true;
        _smtpUser.required = false;
        _smtpPassword.required = false;
      }else {
        _smtpUser.disabled = false;
        _smtpPassword.disabled = false;
        _smtpUser.required = true;
        _smtpPassword.required = true;
      }
     };
  };

  return {
    chart: chart,
    clock: clock,
    loadToTab: loadToTab,
    isChartEmpty: isChartEmpty,
    setApiInfo: setApiInfo,
    createChartToday: createChartToday,
    createChartHour: createChartHour,
    createChartWeek: createChartWeek,
    createChartMonth: createChartMonth,
    smtpSettingsSetup: smtpSettingsSetup
  };

})();
