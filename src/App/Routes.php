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

$app->get('/orders', 'App\Controller\OrderController:index');
$app->get('/orders/{orderId}', 'App\Controller\OrderController:show');
$app->post('/orders', 'App\Controller\OrderController:store');
$app->put('/orders/{orderId}', 'App\Controller\OrderController:update');
$app->delete('/orders/{orderId}', 'App\Controller\OrderController:delete');

$app->get('/member-groups', 'App\Controller\MemberGroupController:index');
$app->post('/member-groups', 'App\Controller\MemberGroupController:store');
$app->delete('/member-groups/{member_id}/{group_id}', 'App\Controller\MemberGroupController:delete');
$app->put('/member-groups/{member_id}/{group_id}', 'App\Controller\MemberGroupController:update');

$app->get('/groups', 'App\Controller\GroupController:index');
$app->get('/groups/{id}', 'App\Controller\GroupController:show');
$app->post('/groups', 'App\Controller\GroupController:store');
$app->put('/groups/{id}', 'App\Controller\GroupController:update');
$app->delete('/groups/{id}', 'App\Controller\GroupController:delete');
