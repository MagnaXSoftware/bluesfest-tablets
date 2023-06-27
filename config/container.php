<?php

declare(strict_types=1);

use App\Doctrine\DBAL\Types\StateEnumType;
use App\Doctrine\Registry;
use App\Form\Extension\Psr7\Psr7Extension;
use App\Twig\DateExtension;
use Cache\Adapter\PHPArray\ArrayCachePool;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\AbstractAsset;
use Doctrine\Migrations\Configuration\Configuration as MigrationConfiguration;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\ORM\Configuration as ORMConfiguration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\Persistence\ManagerRegistry;
use Elao\Enum\Bridge\Twig\Extension\EnumExtension;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Slim\App;
use Slim\Routing\RouteParser;
use Slim\Views\Twig;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Westsworld\TimeAgo;
use function DI\autowire;
use function DI\create;
use function DI\get;

return [
    'root' => dirname(__DIR__),

    'mode' => function (): string {
        // can't do ternary because the assignment needs to be a separate statement
        if (false !== $mode = getenv('APP_MODE'))
            return $mode;

        return 'dev';
    },

    'db.path' => function (ContainerInterface $container): string {
        $path = getenv('DB_PATH');
        if (false !== $path)
            return $path;

        return $container->get('root') . '/tablets.db';
    },
    ORMConfiguration::class => function (ContainerInterface $container): ORMConfiguration {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            [$container->get('root') . '/src/Models'],
            true,
            null,
            $container->get(CacheItemPoolInterface::class)
        );
        $config->setSchemaAssetsFilter(function (string|AbstractAsset $assetName): bool {
            if ($assetName instanceof AbstractAsset) {
                $assetName = $assetName->getName();
            }

            return !in_array($assetName, ['doctrine_migration_versions']);
        });

        return $config;
    },
    EntityManager::class => autowire()->constructorParameter('eventManager', null),
    Connection::class => function (ContainerInterface $container): Connection {
        $conn = DriverManager::getConnection(
            ['driver' => 'pdo_sqlite', 'path' => $container->get('db.path')],
            $container->get(ORMConfiguration::class),
        );
        $conn->getDatabasePlatform()->registerDoctrineTypeMapping(StateEnumType::NAME, StateEnumType::NAME);

        return $conn;
    },
    MigrationConfiguration::class => function (ContainerInterface $container): MigrationConfiguration {
        $config = new MigrationConfiguration();
        $config->setMetadataStorageConfiguration(new TableMetadataStorageConfiguration());
        $config->addMigrationsDirectory('App\Migrations', $container->get('root') . '/src/Migrations');
        $config->setMigrationOrganization(MigrationConfiguration::VERSIONS_ORGANIZATION_NONE);

        return $config;
    },

    CacheItemPoolInterface::class => get(ArrayCachePool::class),
    CacheInterface::class => get(ArrayCachePool::class),
    ArrayCachePool::class => autowire(),

    'template.path' => function (ContainerInterface $container): string {
        return $container->get('root') . '/templates';
    },
    'view' => get(Twig::class),
    Twig::class => function (ContainerInterface $container): Twig {

        $appVariableReflection = new ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
        $vendorTwigBridgeDirectory = dirname($appVariableReflection->getFileName());

        $paths = [$container->get('template.path'), $vendorTwigBridgeDirectory . '/Resources/views/Form'];
        $twig = Twig::create($paths, ['debug' => true, 'auto_reload' => true]);

        $formEngine = new TwigRendererEngine(['tailwind_2_layout.html.twig', '_forms.html.twig'], $twig->getEnvironment());
        $twig->addRuntimeLoader(new FactoryRuntimeLoader([
            FormRenderer::class => function () use ($formEngine) {
                return new FormRenderer($formEngine);
            },
        ]));
        $twig->addExtension(new FormExtension());
        $twig->addExtension(new TranslationExtension($container->get(TranslatorInterface::class)));
        $twig->addExtension(new EnumExtension());
        $twig->addExtension(new DateExtension(null, new TimeAgo()));

        return $twig;
    },
    FormRendererInterface::class => function (Twig $twig): FormRenderer {
        return $twig->getEnvironment()->getRuntime(FormRenderer::class);
    },

    RouteParser::class => function (App $app): RouteParser {
        return $app->getRouteCollector()->getRouteParser();
    },

    FormFactoryInterface::class => function (
        ValidatorInterface    $validator,
        FormRendererInterface $formRenderer,
        TranslatorInterface   $translator,
        ManagerRegistry       $registry
    ): FormFactoryInterface {
        $formFactoryBuilder = new FormFactoryBuilder();
        $formFactoryBuilder->addExtension(new ValidatorExtension($validator, false, $formRenderer, $translator));
        $formFactoryBuilder->addExtension(new Psr7Extension());
        $formFactoryBuilder->addExtension(new DoctrineOrmExtension($registry));

        return $formFactoryBuilder->getFormFactory();
    },
    ManagerRegistry::class => get(Registry::class),
    Registry::class => autowire(),

    TranslatorInterface::class => get(Translator::class),
    Translator::class => create()->constructor('en'),

    ValidatorInterface::class => function (): ValidatorInterface {
        $builder = new ValidatorBuilder();

        return $builder->getValidator();
    }
];