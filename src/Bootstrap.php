<?php

namespace App;

use App\Doctrine\DBAL\Types\StateEnumType;
use DI\Bridge\Slim\Bridge;
use DI\Container;
use DI\ContainerBuilder;
use Slim\App as SlimApp;
use Slim\Views\TwigMiddleware;
use Doctrine\DBAL\Types\Type;


class Bootstrap
{

    private static ?Container $container = null;
    private static ?SlimApp $app = null;

    public static function container(): Container
    {
        if (is_null(self::$container)) {
            Type::addType(StateEnumType::NAME, StateEnumType::class);
            $containerBuilder = new ContainerBuilder();
            $containerBuilder->useAnnotations(false);
            $containerBuilder->useAutowiring(true);

            $containerBuilder->addDefinitions(__DIR__ . '/../config/container.php');

            self::$container = $containerBuilder->build();
        }

        return self::$container;
    }

    public static function app(): SlimApp
    {
        if (is_null(self::$app)) {
            $app = Bridge::create(self::container());
            $app->addRoutingMiddleware();

            require_once __DIR__ . '/routes.php';

            $app->addMiddleware(TwigMiddleware::createFromContainer($app));

            $errorMiddleware = $app->addErrorMiddleware(true, true, true);

            self::$app = $app;
        }

        return self::$app;
    }
}