<?php

class BlogCategory extends Page
{
    public function __construct($params)
    {
        $category = $params['category'];

        $posts = model()->posts->categories->where(['category.slug' => $category])->order('post', 'desc')->fetchAll();

        if (!$posts) {
            $this->isset = false;
            return;
        }

        $this->title = $posts[0]['category.title'];

        $content = '';
        foreach ($posts as $post) {
            $rand = rand();
            $content .= <<<HTML
                <div class="col">
                    <a href="/blog/{$post['category.slug']}/{$post['post.slug']}/" class="card text-decoration-none text-reset">
                        <img loading="lazy" src="https://picsum.photos/320/180?{$rand}" class="card-img-top" alt="{$post['post.title']}">
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