<?php

use App\Form\Extension\Psr7\Psr7Extension;
use App\Storage;
use Elao\Enum\Bridge\Twig\Extension\EnumExtension;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Routing\RouteParser;
use Slim\Views\Twig;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use function DI\autowire;
use function DI\create;
use function DI\get;

return [
    'root' => dirname(__DIR__),

    'db.path' => function (ContainerInterface $container): string {
        $path = getenv('DB_PATH');
        if (false !== $path)
            return $path;

        return $container->get('root') . '/tablets.db';
    },
    'db' => get(Storage::class),
    Storage::class => autowire(),
    PDO::class => function (ContainerInterface $container): PDO {
        return new PDO("sqlite:{$container->get('db.path')}", '', '', [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    },

    'template.path' => function (ContainerInterface $container): string {
        return $container->get('root') . '/templates';
    },
    'view' => get(Twig::class),
    Twig::class => function (ContainerInterface $container): Twig {

        $appVariableReflection = new ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
        $vendorTwigBridgeDirectory = dirname($appVariableReflection->getFileName());

        $paths = [$container->get('template.path'), $vendorTwigBridgeDirectory . '/Resources/views/Form'];
        $twig = Twig::create($paths, ['debug' => true, 'auto_reload' => true]);

        $formEngine = new TwigRendererEngine(['_forms.html.twig'], $twig->getEnvironment());
        $twig->addRuntimeLoader(new FactoryRuntimeLoader([
            FormRenderer::class => function () use ($formEngine) {
                return new FormRenderer($formEngine);
            },
        ]));
        $twig->addExtension(new FormExtension());

        $twig->addExtension(new TranslationExtension($container->get(Translator::class)));

        $twig->addExtension(new EnumExtension());

        return $twig;
    },
    FormRendererInterface::class => function (Twig $twig): FormRenderer {
        return $twig->getEnvironment()->getRuntime(FormRenderer::class);
    },

    RouteParser::class => function (ContainerInterface $container): RouteParser {
        return $container->get(App::class)->getRouteCollector()->getRouteParser();
    },

    FormFactoryInterface::class => function (ValidatorInterface $validator, FormRendererInterface $formRenderer, TranslatorInterface $translator): FormFactoryInterface {
        $formFactoryBuilder = new FormFactoryBuilder();
        $formFactoryBuilder->addExtension(new ValidatorExtension($validator, false, $formRenderer, $translator));
        $formFactoryBuilder->addExtension(new Psr7Extension());

        return $formFactoryBuilder->getFormFactory();
    },

    TranslatorInterface::class => get(Translator::class),
    Translator::class => function (ContainerInterface $container): Translator {
        $translator = new Translator('en');

        return $translator;
    },

    ValidatorInterface::class => function () {
        $builder = new ValidatorBuilder();

        return $builder->getValidator();
    }
];