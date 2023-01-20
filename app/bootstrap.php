<?php

use App\Router;
use App\UserController;
use App\UserRepository;
use DI\Container;
use Doctrine\DBAL\DriverManager;

include __DIR__.'/../vendor/autoload.php';

$container = new Container();

$container->set('db.file', __DIR__ . '/../tests/testdb.sqlite');

$container->set('connection.params', [
    'url' => 'sqlite:///' . $container->get('db.file')
]);
$container->set('Connection', function (Container $c) {
    return DriverManager::getConnection($c->get('connection.params'));
});

$container->set('UserRepository' , function (Container $c){
    return new UserRepository($c->get('Connection'));
});

$container->set('UserController' , function (Container $c){
    return new UserController($c->get('UserRepository'));
});

$container->set('Router', new Router());

return $container;