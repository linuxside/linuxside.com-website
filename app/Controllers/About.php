<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class About
{
    public function index(Request $request)
    {
        return new Response(
            twig()->render('About/About.twig')
        );
    }
}
