<?php

return [
    '/' => 'Home',
    '/blog/' => 'Blog',
    '/blog/add/' => 'BlogPostAdd',
    '/blog/edit/(?<slug>.+)/' => '',
    '/blog/delete/(?<slug>.+)/' => 'BlogPostDelete',
    '/blog/(?<category>[0-9a-zA-Zа-яА-Я_-]+)/' => 'BlogCategory',
    '/blog/(?<category>[0-9a-zA-Zа-яА-Я_-]+)/(?<post>[0-9a-zA-Zа-яА-Я_-]+)/' => 'BlogPost',

    '/login/' => 'Login',
    '/logout/' => 'Logout',
    '/register/' => 'Register',

    '/profile/' => 'Profile',
    '/users/' => 'Users',
    '/user/' => 'User',

    '/api/(?<path>.+)/' => 'Api',
];