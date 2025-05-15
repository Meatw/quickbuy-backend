<?php

class Router
{
    private $routes = [];
    
    public function get($uri, $controller, $options = [])
    {
        $this->addRoute('GET', $uri, $controller, $options);
    }
    
    public function post($uri, $controller, $options = [])
    {
        $this->addRoute('POST', $uri, $controller, $options);
    }
    
    public function put($uri, $controller, $options = [])
    {
        $this->addRoute('PUT', $uri, $controller, $options);
    }
    
    public function delete($uri, $controller, $options = [])
    {
        $this->addRoute('DELETE', $uri, $controller, $options);
    }
    
    private function addRoute($method, $uri, $controller, $options)
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'middleware' => $options['middleware'] ?? null
        ];
    }
    
    public function dispatch($method, $uri)
    {
        // Remove query string
        $uri = parse_url($uri, PHP_URL_PATH);
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            $pattern = $this->convertRouteToRegex($route['uri']);
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove the full match
                
                // Apply middleware if exists
                if ($route['middleware']) {
                    $this->applyMiddleware($route['middleware']);
                }
                
                // Call the controller
                return $this->callController($route['controller'], $matches);
            }
        }
        
        throw new NotFoundException("Route not found: {$uri}");
    }
    
    private function convertRouteToRegex($route)
    {
        $route = preg_replace('/\/{([^}]+)}/', '/([^/]+)', $route);
        return "#^{$route}$#";
    }
    
    private function applyMiddleware($middleware)
    {
        // Parse middleware string (e.g., 'auth:admin')
        $parts = explode(':', $middleware);
        $name = $parts[0];
        $parameter = $parts[1] ?? null;
        
        $middlewareClass = ucfirst($name) . 'Middleware';
        $middlewareFile = __DIR__ . "/../middleware/{$middlewareClass}.php";
        
        if (!file_exists($middlewareFile)) {
            throw new Exception("Middleware not found: {$name}");
        }
        
        require_once $middlewareFile;
        
        $middleware = new $middlewareClass();
        $middleware->handle($parameter);
    }
    
    private function callController($controller, $params)
    {
        list($controllerName, $method) = explode('@', $controller);
        
        $controllerFile = __DIR__ . "/../controllers/{$controllerName}.php";
        
        if (!file_exists($controllerFile)) {
            throw new Exception("Controller not found: {$controllerName}");
        }
        
        require_once $controllerFile;
        
        $controller = new $controllerName();
        
        if (!method_exists($controller, $method)) {
            throw new Exception("Method not found: {$method} in {$controllerName}");
        }
        
        return call_user_func_array([$controller, $method], $params);
    }
}
