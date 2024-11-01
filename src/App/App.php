<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/DotEnv.php';
$app = require __DIR__ . '/Container.php';
$customErrorHandler = require __DIR__ . '/ErrorHandler.php';
(require __DIR__ . '/Middlewares.php')($app, $customErrorHandler);
(require __DIR__ . '/Cors.php')($app);
(require __DIR__ . '/Database.php');
(require __DIR__ . '/Routes.php');
(require __DIR__ . '/NotFound.php')($app);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding, location");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");