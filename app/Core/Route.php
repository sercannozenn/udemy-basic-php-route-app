<?php

namespace app\Core;

class Route
{
    private static array $routes = [];

    private static bool $isRoute = false;

    private static string $where = '([0-9a-zA-Z]+)';

    private static string $prefix = '';

    private static function getUri(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    private static function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function get(string $url, $action): Route
    {
        self::addRoute($url,['GET', 'HEAD'], $action);

        return new self();
    }

    public static function post(string $url, $action): Route
    {
        self::addRoute($url,['POST'], $action);

        return new self();
    }

    private static function addRoute(string $url, array $method, $action): void
    {
        $url = self::$prefix . $url;
        $methodsOriginal = self::$routes[$url]['method'] ?? [];
        $method = array_merge($methodsOriginal, $method);

        self::$routes[$url] = [
            'action' => $action,
            'method' => $method
        ];
    }

    public static function where(array $where): Route
    {
        $url = array_key_last(self::$routes);
        self::$routes[$url]['where']['*'] = self::$where;

        if (!empty($where))
        {
            self::$routes[$url]['where']['custom'] = $where;
        }
        return new self();
    }

    public static function name(string $routeName): Route
    {
        $url = array_key_last(self::$routes);
        self::$routes[$url]['name'] = $routeName;

        return new self();
    }

    public static function prefix(string $prefix): Route
    {
        self::$prefix = $prefix;

        return new self();
    }

    public static function group(\Closure $closure): void
    {
        $closure();
        self::$prefix = '';
    }

    public static function dispatch()
    {
        $uri = self::getUri();
        foreach (self::$routes as $url => $item)
        {
            $url = self::urlParameterReplace($url);
            $pattern = "@^" . $url . "$@";
            if (preg_match($pattern, $uri, $parameters))
            {
                array_shift($parameters);
                self::$isRoute = true;
                self::checkMethod($item['method']);
                self::checkActionIsCallable($item['action'], $parameters);
                self::checkController($item['action'], $parameters);
            }
        }
        self::checkRoute();
    }

    private static function urlParameterReplace(string $url): string
    {
        $where = self::$routes[$url]['where'] ?? ['*' => self::$where];
        preg_match_all("@\{.*?}@", $url, $parameters);
        $parameters = $parameters[0];
        if (count($parameters))
        {
            if (isset($where['custom']))
            {
                foreach ($where['custom'] as $key => $value)
                {
                    $url = str_replace("{" . $key . "}", $value, $url);
                }
            }
            foreach ($parameters as $key => $value)
            {
                $url = str_replace($value,$where['*'], $url);
            }
        }

        return $url;
    }

    private static function checkMethod(array $method): void
    {
        if (!in_array(self::getMethod(), $method))
        {
            $supportedMethods = implode(" | ", $method);
            echo json_encode(['message' => "The " . self::getMethod() . " not supported. Supported methods: $supportedMethods"]);
            exit();
        }
    }

    private static function checkActionIsCallable($action, $parameters): void
    {
        if (is_callable($action))
        {
            call_user_func_array($action, $parameters);
            exit();
        }
    }

    private static function checkController($action, $parameters): void
    {
        $controllerExplode = explode('@', $action);
        $controllerClass = $controllerExplode[0];
        $controllerMethod = $controllerExplode[1];

//        $controllerFile = '../Controllers/' . $controllerClass . '.php';
        $controllerFile = dirname($_SERVER['DOCUMENT_ROOT']) . '/app/Controllers/' . $controllerClass . '.php';
        if (file_exists($controllerFile))
        {
            require_once $controllerFile;
            call_user_func_array([new ('app\Controllers\\' . $controllerClass), $controllerMethod], $parameters);
            exit();
        }
    }

    private static function checkRoute(): void
    {
        if(!self::$isRoute)
        {
//            viewError('404');
            viewError('404', ['message' => 'Aradığınız sayfa bulunamadı.']);
        }
    }

    public static function goUrl(string $routeName, array $parameters = []): void
    {
        $url = array_key_last(array_filter(self::$routes, function($route) use ($routeName){
            return isset($route['name']) && $route['name'] == $routeName;
        }));

        $url = str_replace(array_keys($parameters), array_values($parameters), $url);

        header("Location: $url");
    }

}