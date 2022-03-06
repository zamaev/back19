<?php

return [
    '#^/$#' => 'Home',
    '#/blog/$#' => 'Blog',
    '#/blog/add/#' => 'BlogPostAdd',
    '#/blog/edit/(?<slug>.+)/#' => '',
    '#/blog/delete/(?<slug>.+)/#' => 'BlogPostDelete',
    '#/blog/(?<slug>.+)/#' => 'BlogPost',
];