<?php

declare(strict_types=1);

// auth admin dan staff
$app->get('/', 'App\Controller\HomeController:index');
$app->post('/login', 'App\Controller\AuthController:login');
$app->post('/register', 'App\Controller\AuthController:register');

$app->get('/members', 'App\Controller\MemberController:index');
$app->get('/members/{id}', 'App\Controller\MemberController:show');
$app->post('/members', 'App\Controller\MemberController:store');
$app->put('/members/{id}', 'App\Controller\MemberController:update');
$app->delete('/members/{id}', 'App\Controller\MemberController:delete');