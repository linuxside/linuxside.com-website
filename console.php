#!/usr/bin/env php
<?php

// https://symfony.com/doc/current/console.html
$root = realpath(__DIR__);

require_once $root . '/vendor/autoload.php';

// use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\Console\Application;
use App\Command\CreateBlogPostsCache;

// Debug::enable();
container()->setParameter('root', $root);

$config = include $root . '/app/config.php';
container()->setParameter('config', $config);

$application = new Application();

// Register commands
$application->add(new CreateBlogPostsCache());

$application->run();
