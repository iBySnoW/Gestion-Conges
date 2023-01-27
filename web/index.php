<?php

use Symfony\Component\HttpFoundation\Request;

include '../app/bootstrap.php';

// USER ROUTE
$container->get('Router')->add('/^\/user$/', $container->get('UserController'), 'list');
$container->get('Router')->add('/^\/user\/add$/', $container->get('UserController'), 'add');
$container->get('Router')->add('/^\/user\/delete\/([a-zA-Z0-9-]+)$/', $container->get('UserController'), 'delete');
$container->get('Router')->add('/^\/user\/([a-zA-Z0-9-]+)$/', $container->get('UserController'), 'get');

// CONGE ROUTE
$container->get('Router')->add('/^\/conge\/add$/', $container->get('CongeController'), 'add');
$container->get('Router')->add('/^\/conge\/delete\/([a-zA-Z0-9-]+)$/', $container->get('CongeController'), 'delete');
$container->get('Router')->add('/^\/conge\/employee\/([a-zA-Z0-9-]+)$/', $container->get('CongeController'), 'get');
$container->get('Router')->add('/^\/conge/', $container->get('CongeController'), 'list');

$response = $container->get('Router')->execute(Request::createFromGlobals());
$response->send();