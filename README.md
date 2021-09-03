## Introduction

This blog was made with the help of [Fabien Potencier](http://fabien.potencier.org/) tutorial about [creating your own framework](https://symfony.com/doc/current/create_framework/index.html) with [Symphony](https://symfony.com/) and it is inspired by [Laravel framework](https://laravel.com/). It uses some of the Symphony components and the [Twig](https://twig.symfony.com/) template engine. In the frontend, it uses [Bootstrap 4](https://getbootstrap.com/docs/4.0/getting-started/introduction/) with [jQuery](https://jquery.com/).

Some of its features include:

- flat files storage
- the content uses markup format
- no third-party requests (css, js, etc)
- no cookies usage (or browser storage)
- fast loading time

A *demo* can be seen at [linuxside.com](https://linuxside.com/).

## Requirements

- [PHP](https://www.php.net/) >= 7.3
- [Node.js](https://nodejs.org/) >= 14
- [Composer](https://getcomposer.org/) >= 2
- [Git](https://git-scm.com/) >=2.22

## Installation

**1. Pull the project from github**

```bash
sudo -u www-data git clone \
    https://github.com/linuxside/linuxside.com-website \
    /var/www/linuxside.com
cd linuxside.com
```

**2. Install the PHP dependecies**

```bash
sudo -u www-data composer install
```

**3. Install the Node.js dependecies**

```bash
sudo -u www-data npm install
```

**4. Generate the cache for blog posts**

```bash
chmod u+x ./console.php
sudo -u www-data ./console.php app:blog-posts-cache
```

**Note:** you have to run this command everytime you update any of your posts.

## Running the website locally

```bash
sudo -u www-data php -S localhost:8000 -t ./public
```

## References

- [all Symfony components](https://symfony.com/components)
- [framework routing](https://symfony.com/doc/current/create_framework/routing.html)
- [framework events](https://symfony.com/doc/current/create_framework/event_dispatcher.html)
- [dependecy injection](https://symfony.com/doc/current/create_framework/dependency_injection.html)
- [Google font downloader](https://github.com/neverpanic/google-font-download)
- [browserlist in package.json](https://github.com/browserslist/browserslist#full-list)
