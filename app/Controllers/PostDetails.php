<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Content\BlogPosts;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class PostDetails
{
    private $post;

    public function index(Request $request)
    {
        $this->findDetails($request);

        $data = [];

        $data['post'] = $this->post;

        return new Response(
            twig()->render('Blog/Details.twig', $data)
        );
    }

    private function findDetails(Request $request)
    {
        $url = $request->attributes->get('url');

        // Find the blog post
        foreach (BlogPosts::getInstance()->getAllPosts() as $post) {
            if ($post->url !== $url) continue;
            $this->post = $post;
            break;
        }

        if (is_null($this->post)) {
            throw new ResourceNotFoundException('The blog post was not found');
        }
    }
}
