<?php

declare(strict_types=1);

use Pimple\Container;

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule; 
$capsule->addConnection(array(

	'driver'    => 'mysql',		 
	'host'      => $_ENV['DB_HOST'],		 
	'database'  => $_ENV['DB_NAME'],		 
	'username'  => $_ENV['DB_USER'],		 
	'password'  => $_ENV['DB_PASS'],		 
	'charset'   => 'utf8mb4',		 
	'collation' => 'utf8mb4_unicode_ci',		 
	'prefix'    => '',
	'port'      => $_ENV['DB_PORT']
));
$capsule->setAsGlobal();
$capsule->bootEloquent();

/** @var Container $container */
$container['db'] = static function (): PDO {
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4;port=%s;collation=utf8mb4_unicode_ci',
        $_ENV['DB_HOST'],
        $_ENV['DB_NAME'],
        $_ENV['DB_PORT']
    );
    $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    return $pdo;
};