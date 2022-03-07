<?php

class Blog extends Page
{
    public function __construct()
    {
        $posts = model()->posts->categories->order('post', 'desc')->fetchAll();

        $this->title = 'All posts';

        $content = '';
        foreach ($posts as $post) {
            $rand = rand();
            $content .= <<<HTML
                <div class="col">
                    <a href="/blog/{$post['category.slug']}/{$post['post.slug']}/" class="card text-decoration-none text-reset">
                        <img loading="lazy" src="{$post['post.thumb']}" class="card-img-top" alt="{$post['post.title']}">
                        <div class="card-body">
                            <h5 class="card-title">{$post['post.title']}</h5>
                            <p class="card-text">{$post['post.content']}</p>
                        </div>
                    </a>
                </div>
            HTML;
        }
        $this->vars['title'] = $this->title;
        $this->vars['content'] = $content;
    }
}