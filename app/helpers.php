<?php

use Symfony\Component\Routing;
use Symfony\Component\DependencyInjection;
use Twig\Loader\FilesystemLoader as TwigFilesystemLoader;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\DebugExtension;
use App\Twig\MainExtension;

/**
 * Generates a URL to a public asset filepath
 */
function asset(string $path)
{
    $request = container()->get('request');
    $basePath = $request->getBasePath();

    return sprintf('%s/%s', $basePath, trim($path, '/'));
}

/**
 * Return either one of:
 * - the whole cache
 * - a specific cache key
 * - or if not found, the default value
 */
function cache()
{
    static $cache;

    // Cache the config locally
    if (is_null($cache)) {
        $namespace = '';
        $defaultLifetime = 60;
        $cacheFolder = sprintf('%s/storage/cache', container()->getParameter('root'));

        $cache = new FilesystemAdapter(
            // a string used as the subdirectory of the root cache directory, where cache
            // items will be stored
            $namespace,

            // the default lifetime (in seconds) for cache items that do not define their
            // own lifetime, with a value 0 causing items to be stored indefinitely (i.e.
            // until the files are deleted)
            $defaultLifetime,

            // the main cache directory (the application needs read-write permissions on it)
            // if none is specified, a directory is created inside the system temporary directory
            $cacheFolder
        );
    }

    return $cache;
}

/**
 * Return either one of:
 * - the whole config
 * - a specific config key
 * - or if not found, the default value
 */
function config($key = null, $default = null)
{
    static $config;

    // Cache the config locally
    if (is_null($config)) {
        $config = container()->getParameter('config');
    }

    // The whole config has been asked for
    if (is_null($key)) {
        return $config;
    }

    // A valid key has been asked
    if (isset($config[$key])) {
        return $config[$key];
    }

    // The key was not found, return the default value
    return $default;
}

/**
 * Generate a Symphony container instance, cache it locally and then return it
 */
function container()
{
    static $container;

    // Cache the container locally
    if (is_null($container)) {
        $container = new DependencyInjection\ContainerBuilder();
    }

    return $container;
}

/**
 * Returns the current matching route name
 */
function currentRoute()
{
    $request = container()->get('request');

    if ($request->attributes->has('_route')) {
        return $request->attributes->get('_route');
    }

    return '';
}

/**
 * Generate a URL based on a route with optional parameters
 */
function route(string $routeName, array $routeParameters = [], bool $absolute = false)
{
    static $generator;

    // Cache the URL generator locally
    if (is_null($generator)) {
        $generator = new Routing\Generator\UrlGenerator(
            container()->get('routes'),
            container()->get('context')
        );
    }

    // Generate the URL with Symphony
    $generatedRoute = $generator->generate(
        $routeName,
        $routeParameters,
        $absolute ? Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL : Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH
    );

    // Remove the /index.php/ prefix from the front of the generated routes
    $generatedRoute = str_replace('/index.php/', '/', $generatedRoute);

    return $generatedRoute;
}

/**
 * Generate a Twig instance with some config options, cache it locally and then return it
 */
function twig()
{
    static $twig;

    // Return the cached Twig object
    if (! is_null($twig)) {
        return $twig;
    }

    // Cache Twig new instance locally
    $root = container()->getParameter('root');

    $twigTemplatePaths = [
        sprintf('%s/app/Views', $root),
    ];
    $loader = new TwigFilesystemLoader($twigTemplatePaths);

    // Startup Twig (https://twig.symfony.com/doc/3.x/api.html#environment-options)
    $twig = new TwigEnvironment($loader, [
        'debug'            => config('debug'),
        'charset'          => 'utf-8',
        'cache'            => sprintf('%s/storage/twig', $root),
        'auto_reload'      => true,
        'strict_variables' => config('debug'),
        // 'cache'            => false,
    ]);

    // Add Twig extensions (https://twig.symfony.com/doc/3.x/api.html#using-extensions)
    $twigExtensions = [
        new DebugExtension(),
        new MainExtension(),
    ];
    $twig->setExtensions($twigExtensions);

    return $twig;
}
