<?php

declare(strict_types=1);

namespace App\Auth;

use App\Utils\Routing;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Routing\RouteContext;

class AuthenticationMiddleware implements MiddlewareInterface
{

    private ContainerInterface $container;
    private string $authClassKey;

    public static function createFromContainer(App $app, string $classKey = Authentication::class): self
    {
        return new self($app->getContainer(), $classKey);
    }

    public function __construct(ContainerInterface $container, string $authenticatorKey)
    {
        $this->container = $container;
        $this->authClassKey = $authenticatorKey;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        try {
            /** @var Authentication $auth */
            $auth = $this->container->get($this->authClassKey);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            throw new HttpInternalServerErrorException($request, 'An authentication class was not found with key ' . $this->authClassKey, $e);
        }

        $user = new AnonymousUser();

        $routeContext = RouteContext::fromRequest($request);

        if (($route = $routeContext->getRoute()) !== null) {
            // Should always happen, as routing happens first
            $attributes = Routing::routeAttributes($route);
            print_r($attributes);

            //new Reflection
        }

        $request = $request->withAttribute(User::USER_KEY, $user);
        return $handler->handle($request);
    }
}