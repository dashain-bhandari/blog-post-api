<?php 
class Router
{
    private array $routes = [];
    private ServiceContainer $container;

    public function __construct(ServiceContainer $container)
    {
        $this->container = $container;
    }

    public function add(string $method, string $path, array $controller, array $middlewares = []): void
    {
        $path = $this->normalizePath($path);
        $this->routes[] = [
            'path' => $path,
            'method' => strtoupper($method),
            'controller' => $controller,
            'middlewares' => $middlewares,  // Store middlewares for this route
        ];
    }

    private function normalizePath(string $path): string
    {
        $path = trim($path, '/');
        return "/{$path}";
    }

    public function dispatch(string $path)
    {
        $path = $this->normalizePath($path);
        $method = strtoupper($_SERVER['REQUEST_METHOD']);

        foreach ($this->routes as $route) {
            $pattern = preg_replace('#\{[^\}]+\}#', '([^/]+)', $route['path']);
            $pattern = "#^{$pattern}$#";

            if (preg_match($pattern, $path, $matches) && $route['method'] === $method) {
                array_shift($matches); // Remove the full match

                // Execute middlewares before the controller
                foreach ($route['middlewares'] as $middleware) {
                    $middlewareInstance = $this->container->get($middleware);
                    if (!$middlewareInstance->handle()) {
                        return;  // Stop if any middleware fails
                    }
                }

                [$class, $function] = $route['controller'];

                // Resolve controller and inject dependencies
                $controllerInstance = $this->container->get($class);

                // Call the controller method with parameters
                $controllerInstance->{$function}(...$matches);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Route not found"]);
    }
}
