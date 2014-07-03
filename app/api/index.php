<?php
define("AUTOLOAD_PATH",'../lib/classes/');
require_once '../lib/head.inc';      // will cause fail if not logged in!!!
require_once 'vendor/autoload.php';

$app = new \Slim\Slim();

// Graph API
$app->group('/graph', function() use ($app) {

  /**
   * Check if given span is valid
   * @param  SlimRoute $route
   */
  function verifySpan(\Slim\Route $route){
    $span = $route->getParams()['span'];
    $valid = '(hour|day|week|month)';
    if (!preg_match($valid, $span)) {
      $app = \Slim\Slim::getInstance();

      echo 'Specified span <strong>'.$span.'</strong> is not valid.<br>';
      echo 'Use '.$valid;

      // Stop with bad request
      $app->status(400);
      $app->stop();
    }
  }

  // Graph for predefined span
  $app->get('/span/:span', 'verifySpan', function($span) use ($app) {
    $api = new Snilius\RackTemp\Api\GraphApi();
    $graphData = $api->getSpan($span);

    $app->contentType('text/csv');
    echo $graphData;

  });

  $app->get('/range/:start/:stop',function($start, $stop) use ($app) {

  });

});

// $app->get('/graph/:span', function($span){


//   // $api->getSpanGraph($span);

// });

// $app->get('/graph/range/:start/:stop',function($start, $stop){

// });

$app->get('/', function(){
  // serve api doc, or just link github
});

$app->run();

 ?>
