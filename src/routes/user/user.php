<?php

use App\Controller\UserController;
use App\Model\TokenModel;
use App\Model\UserModel;
use Slim\Routing\RouteCollectorProxy;
use Slim\App;

return function (App $app, $pdo) {
    $app->group('/user', function (RouteCollectorProxy $group) use ($pdo) {
        $tokenModel = new TokenModel($pdo);
        $userModel = new UserModel($pdo);
        $userController = new UserController($tokenModel, $userModel);

        $group->get('/getAll', [$userController, 'getAll']);
        $group->post('/create', [$userController, 'createUser']);
        $group->post('/update/{id}', [$userController, 'updateUser']);
    });
};