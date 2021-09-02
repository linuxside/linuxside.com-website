<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Content\BlogPosts;

class Homepage
{
    public function index(Request $request)
    {
        $data = [];

        $data['allPosts'] = BlogPosts::getInstance()->getAllPosts();

        return new Response(
            twig()->render('Homepage/Homepage.twig', $data)
        );
    }
}
