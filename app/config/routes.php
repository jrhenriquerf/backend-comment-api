<?php

$usersCollection = new \Phalcon\Mvc\Micro\Collection();
$usersCollection->setHandler('\App\Controllers\UserController', true);
$usersCollection->setPrefix('/user');
$usersCollection->post('/', 'addAction');
$usersCollection->get('/', 'getAllAction');
$usersCollection->put('/{userId:[1-9][0-9]*}', 'updateAction');
$usersCollection->delete('/{userId:[1-9][0-9]*}', 'deleteAction');
$app->mount($usersCollection);

$postCollection = new \Phalcon\Mvc\Micro\Collection();
$postCollection->setHandler('\App\Controllers\PostController', true);
$postCollection->setPrefix('/post');
$postCollection->post('/', 'addAction');
$postCollection->get('/', 'getAllAction');
$postCollection->get('/{postId:[1-9][0-9]*}', 'getAction');
$postCollection->put('/{postId:[1-9][0-9]*}', 'updateAction');
$postCollection->delete('/{postId:[1-9][0-9]*}', 'deleteAction');
$app->mount($postCollection);

$commentCollection = new \Phalcon\Mvc\Micro\Collection();
$commentCollection->setHandler('\App\Controllers\CommentController', true);
$commentCollection->setPrefix('/comment');
$commentCollection->post('/', 'addAction');
$commentCollection->get('/', 'getAllAction');
$commentCollection->get('/{commentId:[1-9][0-9]*}', 'getAction');
$commentCollection->delete('/{commentId:[1-9][0-9]*}', 'deleteAction');
$commentCollection->delete('/deleteAll/{postId:[1-9][0-9]*}', 'deleteAllAction');
$app->mount($commentCollection);

$notificationCollection = new \Phalcon\Mvc\Micro\Collection();
$notificationCollection->setHandler('\App\Controllers\NotificationController', true);
$notificationCollection->setPrefix('/notifications');
$notificationCollection->get('/{userId:[1-9][0-9]*}', 'getUserNotificationsAction');
$app->mount($notificationCollection);

$app->notFound(
  function () use ($app) {
      $exception =
        new \App\Exceptions\HttpExceptions\Http404Exception(
          _('URI not found or error in request.'),
          ['URI not found: ' . $app->request->getMethod() . ' ' . $app->request->getURI()]
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