<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Controller\AccessController;
use App\Model\AccessModel;

return function (App $app, $pdo) {
    $app->group('/access', function (RouteCollectorProxy $group) use ($pdo) {
        $accessModel = new AccessModel($pdo);
        $accessController = new AccessController($accessModel);

        $group->post('/login', [$accessController, 'login']);
    });
};