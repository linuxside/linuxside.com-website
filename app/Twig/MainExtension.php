<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * See twig\twig\src\Extension\CoreExtension.php to find out more about the methods used in this class.
 * https://twig.symfony.com/doc/2.x/advanced.html
 */
class MainExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            // Functions
            new TwigFunction('asset', 'asset'),
            new TwigFunction('route', 'route'),
            new TwigFunction('config', 'config'),
            new TwigFunction('currentRoute', 'currentRoute'),
            new TwigFunction('dd', 'dd'),
        ];
    }
}