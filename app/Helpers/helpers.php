<?php

if (!function_exists('dd'))
{
    function dd(mixed $arr)
    {
        print_r($arr);
        die();
    }
}

if (!function_exists('viewError'))
{
    function viewError(string $phpFile, array $data = [])
    {
        $filePath = dirname($_SERVER['DOCUMENT_ROOT']) . '/resources/views/errors/' . $phpFile . '.php';
        if (file_exists($filePath))
        {
            extract($data);
            return require_once $filePath;
        }
        return require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/resources/views/errors/404.php';
    }
}


if (!function_exists('view'))
{
    function view(string $phpFile)
    {
        $filePath = dirname($_SERVER['DOCUMENT_ROOT']) . '/resources/views/' . $phpFile . '.php';
        if (file_exists($filePath))
        {
            return require_once $filePath;
        }
        return require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/resources/views/errors/404.php';
    }
}
if (!function_exists('route'))
{
    function route(string $routeName, array $parameters = []): void
    {
        \app\Core\Route::goUrl($routeName, $parameters);
    }
}