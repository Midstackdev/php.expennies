<?php

declare(strict_types = 1);

use App\Config;
use App\Enum\AppEnvironment;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollection;
use Symfony\WebpackEncoreBundle\Asset\TagRenderer;
use Symfony\WebpackEncoreBundle\Twig\EntryFilesTwigExtension;
use Twig\Extra\Intl\IntlExtension;

use function DI\create;

return [
    App::class => function(ContainerInterface $container) {

        $addMiddlewares = require CONFIG_PATH . '/middleware.php';
        $router = require CONFIG_PATH . '/routes/web.php';

        AppFactory::setContainer($container);

        $app = AppFactory::create();

        $router($app);
        $addMiddlewares($app);

        return $app;
    },
    Config::class                 => create(Config::class)->constructor(require CONFIG_PATH . '/app.php'),
    'orm_config'                  => fn(Config $config) => ORMSetup::createAttributeMetadataConfiguration(
            $config->get('doctrine.entity_dir'),
            $config->get('doctrine.dev_mode')
    ),
    EntityManager::class          => fn(Config $config, ContainerInterface $container) => new EntityManager(
        DriverManager::getConnection($config->get('doctrine.connection'), $container->get('orm_config')),
        $container->get('orm_config')
    ),
    Twig::class                   => function (Config $config, ContainerInterface $container) {
        $twig = Twig::create(VIEW_PATH, [
            'cache'       => STORAGE_PATH . '/cache/templates',
            'auto_reload' => AppEnvironment::isDevelopment($config->get('app_environment')),
        ]);

        $twig->addExtension(new IntlExtension());
        $twig->addExtension(new EntryFilesTwigExtension($container));
        $twig->addExtension(new AssetExtension($container->get('webpack_encore.packages')));

        return $twig;
    },
    /**
     * The following two bindings are needed for EntryFilesTwigExtension & AssetExtension to work for Twig
     */
    'webpack_encore.entrypoint_lookup_collection' => fn() => new EntrypointLookupCollection(
        new ServiceLocator(['_default' => fn () =>
            new EntrypointLookup(BUILD_PATH . '/entrypoints.json')
        ])
    ),
    'webpack_encore.packages' => fn() => new Packages(
        new Package(new JsonManifestVersionStrategy(BUILD_PATH . '/manifest.json'))
    ),
    'webpack_encore.tag_renderer' => fn(ContainerInterface $container) => new TagRenderer(
        $container->get('webpack_encore.entrypoint_lookup_collection'),
        $container->get('webpack_encore.packages')
    ),

    ResponseFactoryInterface::class => fn(App $app) => $app->getResponseFactory(),
];
