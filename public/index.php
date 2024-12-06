<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/database.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

(require __DIR__ . '/../src/routes/user/user.php')($app, $pdo);
(require __DIR__ . '/../src/methods/login/login.php')($app, $pdo);

$app->run();