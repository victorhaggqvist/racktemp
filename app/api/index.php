<?php
require_once '../lib/head.inc';

$app = new \Slim\Slim(array(
    'log.level' => \Slim\Log::DEBUG,
    'mode' => 'development',
    'debug' => true
));

$app->add(new \Snilius\RackTemp\Api\ApiAuthMiddleware());

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
    $api = new \Snilius\RackTemp\Api\GraphApi();
    $graphData = $api->getSpan($span);

    $app->contentType('application/json');
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
  echo 'Gz auth worked';
  // serve api doc, or just link github
});

$app->get('/test', function(){
  echo 'test';
});

$app->get('/test/mail/:debugMode', function($debugMode) {
  $mailer = new \Snilius\RackTemp\Mailer();
  $mailer->enableDebug($debugMode);
  var_dump($mailer->sendTest());
});

$app->run();

 ?>
