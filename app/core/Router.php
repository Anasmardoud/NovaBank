<?php
class Router
{
    private $routes = [];

    public function addRoute($route, $controller, $action)
    {
        $this->routes[$route] = ['controller' => $controller, 'action' => $action];
    }

    public function dispatch($uri)
    {
        // Remove query strings from the URI
        $uri = strtok($uri, '?');

        // Check if the URI exists in the routes array
        if (array_key_exists($uri, $this->routes)) {
            $controller = $this->routes[$uri]['controller'];
            $action = $this->routes[$uri]['action'];

            // Instantiate the controller and call the action
            $controllerInstance = new $controller();
            $controllerInstance->$action();
        } else {
            // Handle 404 Not Found
            header('HTTP/1.1 404 Not Found');
            echo 'Page not found';
        }
    }
}
