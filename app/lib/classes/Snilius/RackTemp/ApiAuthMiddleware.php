<?php

// namespace Snilius\RackTemp;

// require_once LIB_PATH.'/../vendor/autoload.php';

// use Snilius\RackTemp\Api;
// use Slim\Middleware;

// /**
// * Slim middleware for basic http auth
// */
// class ApiAuthMiddleware extends \Slim\Middleware {

//   public function call() {
//     // Get reference to application
//     $app = $this->app;

//     $api = new Api();

//     $timestamp = $_SERVER['PHP_AUTH_USER'];
//     $token = $_SERVER['PHP_AUTH_PW'];

//     if ($api->checkKeyPair($timestamp, $token)) {
//       echo 'all good';
//     }else{
//       echo 'booooooooooooo';
//       $app->stop();
//     }

//     // Run inner middleware and application
//     $this->next->call();

//   }
// }

 ?>
