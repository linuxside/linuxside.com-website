<?php

namespace App\Content;

use Symfony\Component\Finder\Finder;
use League\CommonMark\Environment;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\CommonMarkConverter;
use App\Libraries\Str;
use stdClass;

class BlogPosts
{
    private static $instance;
    private array $featured = [];
    private array $nonFeatured = [];
    private array $allPosts = [];

    const FEATURED_POSTS_CACHE_KEY = 'featured_posts';
    const NON_FEATURED_POSTS_CACHE_KEY = 'non_featured_posts';
    const ALL_POSTS_CACHE_KEY = 'all_posts';

    /**
     * Get the globally available instance of the BlogPosts.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Returns the locally parsed/cached featured blog posts.
     *
     * @return array
     */
    public function getFeatured()
    {
        return $this->featured;
    }

    /**
     * Returns the locally parsed/cached blog posts that are NOT featured.
     *
     * @return array
     */
    public function getNonFeatured()
    {
        return $this->nonFeatured;
    }

    /**
     * Returns the locally parsed/cached blog posts.
     *
     * @return array
     */
    public function getAllPosts()
    {
        return $this->allPosts;
    }

    /**
     * Get the blog post from cache.
     * https://symfony.com/doc/current/components/cache.html
     *
     * @return void
     */
    public function loadContentFromCache()
    {
        // Get featured posts
        $featuredPosts = cache()->getItem(self::FEATURED_POSTS_CACHE_KEY);

        if ($featuredPosts->isHit()) {
            $this->featured = $featuredPosts->get();
        }

        // Get non-featured posts
        $nonFeaturedPosts = cache()->getItem(self::NON_FEATURED_POSTS_CACHE_KEY);

        if ($nonFeaturedPosts->isHit()) {
            $this->nonFeatured = $nonFeaturedPosts->get();
        }

        // Get all posts
        $allPosts = cache()->getItem(self::ALL_POSTS_CACHE_KEY);

        if ($allPosts->isHit()) {
            $this->allPosts = $allPosts->get();
        }
    }

    /**
     * Parses all the blog post files and cache them locally.
     * https://symfony.com/doc/current/components/cache.html
     *
     * @return void
     */
    public function parseAndCacheContent()
    {
        // Delete the old cache
        cache()->delete(self::FEATURED_POSTS_CACHE_KEY);
        cache()->delete(self::NON_FEATURED_POSTS_CACHE_KEY);
        cache()->delete(self::ALL_POSTS_CACHE_KEY);

        $this->parsePosts();

        // Cache the posts
        // https://symfony.com/doc/current/components/cache/cache_items.html
        {
            // Featured posts
            $featuredPosts = cache()->getItem(self::FEATURED_POSTS_CACHE_KEY);
            $featuredPosts->expiresAfter(strtotime('1 year'));
            $featuredPosts->set($this->featured);
            cache()->save($featuredPosts);

            // Non-featured posts
            $nonFeaturedPosts = cache()->getItem(self::NON_FEATURED_POSTS_CACHE_KEY);
            $nonFeaturedPosts->expiresAfter(strtotime('1 year'));
            $nonFeaturedPosts->set($this->nonFeatured);
            cache()->save($nonFeaturedPosts);

            // All posts
            $allPosts = cache()->getItem(self::ALL_POSTS_CACHE_KEY);
            $allPosts->expiresAfter(strtotime('1 year'));
            $allPosts->set($this->allPosts);
            cache()->save($allPosts);
        }
    }

    /**
     * Parse the blog post files.
     *
     * @return void
     */
    private function parsePosts()
    {
        $files = $this->retrieveListOfPosts();

        foreach ($files as $file) {
            $post = $this->parseFile(
                $file->getContents(),
                // $this->filenameToUrl($file->getBasename())
            );

            $this->allPosts[] = $post;

            if ($post->featured) {
                $this->featured[] = $post;
            } else {
                $this->nonFeatured[] = $post;
            }
        }
    }

    /**
     * Retrieve a list with the blog posts.
     * This is done by reading all the files in the posts directory and storing them in an array.
     *
     * @return array
     */
    private function retrieveListOfPosts()
    {
        $postsPath = sprintf('%s/posts', container()->getParameter('root'));

        $finder = new Finder();
        $finder->files();
        $finder->in($postsPath);
        $finder->name('/\.md$/');
        $finder->depth('== 0');
        $finder->sortByName()->reverseSorting();

        $files = [];
        foreach ($finder as $file) {
            $files[] = $file;
        }

        return $files;
    }

    /**
     * Parse a single blog post.
     *
     * @return stdClass
     */
    private function parseFile(string $fileContent, string $url = '')
    {
        $post = new stdClass;
        $post->title    = '';
        $post->date     = '';
        $post->tags     = '';
        $post->featured = false;
        $post->summary  = '';
        $post->content  = '';
        $post->url      = $url;

        $contentLines = explode("\n", $fileContent);

        $headerStarted = false;
        $summaryStarted = false;
        $contentStarted = false;
        foreach ($contentLines as $line) {
            // Content delimiters
            if ($line === '---') {
                if (! $headerStarted) {
                    $headerStarted = true;
                    continue;
                }
                if (! $summaryStarted) {
                    $summaryStarted = true;
                    continue;
                }
                if (! $contentStarted) {
                    $contentStarted = true;
                    continue;
                }
            }

            // Content header
            if ($headerStarted && ! $summaryStarted) {
                // Post title
                $startTag = 'title:';
                if (strpos($line, $startTag) === 0) {
                    $post->title = trim(substr($line, strlen($startTag)));
                }

                // Post date
                $startTag = 'date:';
                if (strpos($line, $startTag) === 0) {
                    $post->date = trim(substr($line, strlen($startTag)));
                }

                // Post tags
                $startTag = 'tags:';
                if (strpos($line, $startTag) === 0) {
                    $post->tags = substr($line, strlen($startTag));
                    $post->tags = explode(',', $post->tags);
                    $post->tags = array_map('trim', $post->tags);
                }

                // Post featured
                $startTag = 'featured:';
                if (strpos($line, $startTag) === 0) {
                    $post->featured = trim(substr($line, strlen($startTag))) === 'true';
                }
            }

            // Content summary
            if ($summaryStarted && ! $contentStarted) {
                $post->summary .= $line;
                $post->summary .= "\n";
            }

            // Actual content
            if ($contentStarted) {
                $post->content .= $line;
                $post->content .= "\n";
            }
        }

        $post->summary = $this->markdownToHtml($post->summary);
        $post->content = $this->markdownToHtml($post->content);

        $post->url = Str::slug($post->title);

        return $post;
    }

    /**
     * Transform the blog filename into useable URL.
     *
     * @return stdClass
     */
    private function filenameToUrl(string $filename)
    {
        // Remove the numbering
        $expl = explode('-', $filename);
        array_shift($expl);
        $filename = implode('-', $expl);

        // Remove the extension
        $expl = explode('.', $filename);
        array_pop($expl);
        $filename = implode('.', $expl);

        return $filename;
    }

    /**
     * Convert markdown string to HTML.
     *
     * @return string
     */
    private function markdownToHtml(string $markdown)
    {
        static $converter;

        if (is_null($converter)) {
            $environment = Environment::createCommonMarkEnvironment();

            // https://commonmark.thephpleague.com/1.6/extensions/overview/
            $environment->addExtension(new ExternalLinkExtension());

            // https://commonmark.thephpleague.com/1.6/extensions/external-links/
            $environment->mergeConfig([
                'external_link' => [
                    'internal_hosts'     => config('domain'),
                    'open_in_new_window' => true,
                    'html_class'         => '',
                    'nofollow'           => 'external',
                    'noopener'           => 'external',
                    'noreferrer'         => 'external',
                ],
            ]);

            $config = [
                'html_input'         => 'strip',
                'allow_unsafe_links' => false,
            ];
            $converter = new CommonMarkConverter($config, $environment);
        }

        return $converter->convertToHtml(
            trim($markdown)
        );
    }
}
