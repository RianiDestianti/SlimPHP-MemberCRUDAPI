<?php

declare(strict_types=1);

// auth admin dan staff
$app->get('/', 'App\Controller\HomeController:index');
$app->post('/login', 'App\Controller\AuthController:login');
$app->post('/register', 'App\Controller\AuthController:register');