<?php


namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{
    private $routes = [];

    public function add(string $route, Controller $controller, string $method)
    {
        $this->routes[$route] = [
            'controller' => $controller,
            'method' => $method,
        ];
    }

    public function execute(Request $request):Response
    {
        foreach ($this->routes as $route => $action) {
            if(preg_match($route, $request->getPathInfo(), $matches)){
                array_shift($matches);
                array_push($matches, $request);
                return $action['controller']->{$action['method']}(...$matches);
            }
        }
        return new Response('', Response::HTTP_NOT_FOUND);
    }
}