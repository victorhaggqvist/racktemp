<?php

namespace Snilius\RackTemp\Api;

/**
* Slim middleware for basic http auth
*/
class ApiAuthMiddleware extends \Slim\Middleware {

  public function call() {
    // Get reference to application
    $app = $this->app;

    $api = new \Snilius\RackTemp\Api\Api();

    if (!isset($_SERVER['PHP_AUTH_USER'])) {
      header('WWW-Authenticate: Basic realm="My Realm"');
      header('HTTP/1.0 401 Unauthorized');
      echo '401 Unauthorized';
      exit;
    } else {

      $timestamp = $_SERVER['PHP_AUTH_USER'];
      $token = $_SERVER['PHP_AUTH_PW'];

      if ($api->checkKeyPair($timestamp, $token)) {
        // All good, run application
        $this->next->call();
      }else{
        echo 'Invalid token';
        if ($timestamp == 'tea')
          $app->status(418);
        else
          $app->status(401);
      }
    }
  }
}

 ?>
