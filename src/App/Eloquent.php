<?php
    use Illuminate\Database\Capsule\Manager as Capsule;

    $capsule = new Capsule; 
    $capsule->addConnection(array(
    
        'driver'    => 'mysql',		 
        'host'      => $_SERVER['DB_HOST'],		 
        'database'  => $_SERVER['DB_NAME'],		 
        'username'  => $_SERVER['DB_USER'],		 
        'password'  => $_SERVER['DB_PASS'],		 
        'charset'   => 'utf8',		 
        'collation' => 'utf8_unicode_ci',		 
        'prefix'    => ''
    
    ));
    $capsule->setAsGlobal();
    $capsule->bootEloquent();