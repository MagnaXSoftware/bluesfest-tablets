<?php

declare(strict_types=1);

namespace App\Utils;

use Closure;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use Slim\Interfaces\AdvancedCallableResolverInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Routing\Route;

class Routing {

}

function routeToCallable(Route $route): callable
{
    $reflectionRoute = new ReflectionClass($route);

    $callableResolverProperty = $reflectionRoute->getProperty('callableResolver');
    /** @var CallableResolverInterface $callableResolver */
    $callableResolver = $callableResolverProperty->getValue($route);
    $callable = $route->getCallable();

    if ($callableResolver instanceof AdvancedCallableResolverInterface) {
        $callable = $callableResolver->resolveRoute($callable);
    } else {
        $callable = $callableResolver->resolve($callable);
    }

    return $callable;
}

/**
 * @return ReflectionAttribute[]
 */
function routeAttributes(Route $route): array
{
    $callable = routeToCallable($route);
    try {
        if ($callable instanceof Closure) {
            $reflector = new ReflectionFunction($callable);
            return $reflector->getAttributes();
        }

        if (is_string($callable) && function_exists($callable)) {
            $reflector = new ReflectionFunction($callable);
            return $reflector->getAttributes();
        }

        if (is_string($callable) && str_contains($callable, '::')) {
            $reflector = new ReflectionMethod($callable);
            return $reflector->getAttributes();
        }

        if (is_object($callable) && method_exists($callable, '__invoke')) {
            $reflector = new ReflectionMethod($callable, '__invoke');
            return $reflector->getAttributes();
        }

        if (is_array($callable)) {
            $reflector = new ReflectionMethod($callable[0], $callable[1]);
            return $reflector->getAttributes();
        }
    } catch (ReflectionException $exception) {
        return [];
    }
    return [];
}