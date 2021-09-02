<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NotFound
{
    public function index(Request $request)
    {
        return new Response(
            twig()->render('Error/NotFound.twig'),
            Response::HTTP_NOT_FOUND
        );
    }
}
