<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Routing\RouteParser;
use Slim\Views\Twig;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class Controller
{

    protected App $app;
    protected ResponseFactory $responseFactory;
    protected Twig $twig;
    protected RouteParser $routeParser;
    protected FormFactoryInterface $formFactory;

    public function __construct(App $app, ResponseFactory $responseFactory, Twig $twig, RouteParser $routeParser, FormFactoryInterface $formFactory)
    {
        $this->app = $app;
        $this->responseFactory = $responseFactory;
        $this->twig = $twig;
        $this->routeParser = $routeParser;
        $this->formFactory = $formFactory;
    }

    protected function redirect(string $location, int $code = 302): ResponseInterface
    {
        return $this->responseFactory->createResponse($code)->withHeader('Location', $location);
    }

    protected function urlFor(string $name, array $args = []): string
    {
        return $this->routeParser->urlFor($name, $args);
    }

    protected function convertPsr7Request(ServerRequestInterface $request): Request
    {
        $httpFoundationFactory = new HttpFoundationFactory();

        return $httpFoundationFactory->createRequest($request);
    }

}