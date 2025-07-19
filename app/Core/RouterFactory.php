<?php

declare(strict_types=1);

namespace App\Core;

use Nette;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
    use Nette\StaticClass;

    public static function createRouter(): RouteList
    {
        $router = new RouteList();
        $router->addRoute('articles[/<id>]', 'Articles:default');
        $router->addRoute('users[/<id>]', 'Users:default');
        $router->addRoute('auth/register', 'Auth:register');
        $router->addRoute('auth/login', 'Auth:login');
        return $router;
    }
}
