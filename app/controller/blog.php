<?php

class Blog extends Page
{
    protected $template = 'app/view/blog.html';

    public function __construct()
    {
        $model = Model::getInstance();
        $posts = $model->posts()->fetchAll();

        $this->title = 'All posts';
        $this->content = '';

        foreach ($posts as $post) {
            $this->content .= <<<HTML
                <section>
                    <h2>{$post['title']}</h2>
                    <p>{$post['content']}</p>
                <section>
            HTML;
        }
    }
}