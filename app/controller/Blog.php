<?php

class Blog extends Page
{
    public function __construct()
    {
        $model = Model::getInstance();
        $posts = $model->posts()->order('post', 'desc')->fetchAll();

        $this->title = 'All posts';

        $content = '';
        foreach ($posts as $post) {
            $rand = rand();
            $content .= <<<HTML
                <div class="col">
                    <a href="./{$post['slug']}/" class="card text-decoration-none text-reset">
                        <img loading="lazy" src="https://picsum.photos/320/180?{$rand}" class="card-img-top" alt="{$post['title']}">
                        <div class="card-body">
                            <h5 class="card-title">{$post['title']}</h5>
                            <p class="card-text">{$post['content']}</p>
                        </div>
                    </a>
                </div>
            HTML;
        }
        $this->vars['title'] = $this->title;
        $this->vars['content'] = $content;
    }
}