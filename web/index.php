<?php

use Symfony\Component\HttpFoundation\Request;

include '../app/bootstrap.php';

$container->get('Router')->add('/^\/user$/', $container->get('UserController'), 'list');
$container->get('Router')->add('/^\/user\/add$/', $container->get('UserController'), 'add');
$container->get('Router')->add('/^\/user\/delete\/([a-zA-Z0-9-]+)$/', $container->get('UserController'), 'delete');
$container->get('Router')->add('/^\/user\/([a-zA-Z0-9-]+)$/', $container->get('UserController'), 'get');

$response = $container->get('Router')->execute(Request::createFromGlobals());
$response->send();