<?php

class Blog extends Page
{
    protected $template = 'app/view/blog.html';

    public function __construct()
    {
        $model = Model::getInstance();
        $posts = $model->posts()->fetchAll();

        $this->title = 'All posts';

        $content = '';
        foreach ($posts as $post) {
            $rand = rand();
            $content .= <<<HTML
                <div class="card" style="width: 18rem;">
                    <img src="https://picsum.photos/320/180?{$rand}" class="card-img-top" alt="{$post['title']}" widht="320" height="180">
                    <div class="card-body">
                        <h5 class="card-title">{$post['title']}</h5>
                        <p class="card-text">{$post['content']}</p>
                        <a href="./{$post['post']}/" class="btn btn-primary">Go</a>
                    </div>
                </div>
            HTML;
        }
        $this->vars['title'] = $this->title;
        $this->vars['content'] = $content;
    }
}