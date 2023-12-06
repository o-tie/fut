<?php

namespace core;

class Router
{
    protected string $uri;
    protected string $method;
    protected array $routes;

    protected const METHOD_GET = 'GET';
    protected const METHOD_PUT = 'PUT';
    protected const METHOD_PATCH = 'PATCH';
    protected const METHOD_POST = 'POST';
    protected const METHOD_DELETE = 'DELETE';

    public function __construct($uri, $method)
    {
        $this->method = $method;
        $this->uri = $this->method === self::METHOD_GET ? explode('?', $uri)[0] : $uri;
        $this->routes = require_once(__DIR__ . '/../routes/web.php');
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        // Get current URI
        if ($this->uri === '/favicon.ico') {
            return;
        }
        $routes = $this->routes[$this->method] ?? [];

        // Get current URI
        if (array_key_exists($this->uri, $routes)) {
            // If it matches, call the corresponding controller method
            $handler = $routes[$this->uri];
            $controller = $handler[0];
            $method = $handler[1];

            if (in_array($this->method, [self::METHOD_PATCH, self::METHOD_PUT, self::METHOD_POST])) {
                $jsonPayload = file_get_contents('php://input');
                $params = json_decode($jsonPayload, true);
            } else {
                $params = $_GET;
            }
            // Call the controller method, passing the parameters
            (new $controller())->$method($params);
        } else {
            // Route not found
            header("HTTP/1.0 404 Not Found");
            echo '404 Not Found';
        }
    }
}
