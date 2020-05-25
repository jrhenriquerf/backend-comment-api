<?php

$usersCollection = new \Phalcon\Mvc\Micro\Collection();
$usersCollection->setHandler('\App\Controllers\UserController', true);
$usersCollection->setPrefix('/user');
$usersCollection->post('/', 'addAction');
$usersCollection->get('/', 'getAllAction');
$usersCollection->put('/{userId:[1-9][0-9]*}', 'updateAction');
$usersCollection->delete('/{userId:[1-9][0-9]*}', 'deleteAction');
$app->mount($usersCollection);

$app->notFound(
  function () use ($app) {
      $exception =
        new \App\Controllers\HttpExceptions\Http404Exception(
          _('URI not found or error in request.'),
          new \Exception('URI not found: ' . $app->request->getMethod() . ' ' . $app->request->getURI())
        );
      throw $exception;
  }
);

$app->after(
  function () use ($app) {
      $return = $app->getReturnedValue();

      if (is_array($return)) {
          $app->response->setContent(json_encode($return));
      } elseif (!strlen($return)) {
          $app->response->setStatusCode('204', 'No Content');
      } else {
          throw new Exception('Bad Response');
      }

      $app->response->send();
  }
);