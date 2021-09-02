<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Content\BlogPosts;

class CreateBlogPostsCache extends Command
{
    protected static $defaultName = 'app:blog-posts-cache';

    protected function configure(): void
    {
        $this->setDescription('Creates the blog posts cache out of markup blog files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Parsing markup files and caching locally...");

        BlogPosts::getInstance()->parseAndCacheContent();

        $output->writeln('<info>Done!</info>');

        return Command::SUCCESS;
    }
}
