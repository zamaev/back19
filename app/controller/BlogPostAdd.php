<?php

class BlogPostAdd extends Page
{
    public function __construct()
    {
        $this->title = 'Add post';

        $title = $_POST['post']['title'] ?? null;
        $slug = $_POST['post']['slug'] ?? null;
        $content = $_POST['post']['content'] ?? null;
        $thumb = $_POST['post']['thumb'] ?? null;

        $_POST['post']['category__id'] = $_POST['post']['category__id'] ?? 1;
        
        if ($title && $slug && $content) {
            if (!model()->post(['slug' => $slug])) {
                $post = model()->post();
                $post_data = $post->setData($_POST['post'])->save()->data();
                header('Location: /blog/'.$post_data['slug'].'/');
            
            } else {
                $this->vars['error'] = '<p class="text-danger fw-bold">This slug is exist</p>';
                $this->vars['title'] = $title;
                $this->vars['slug'] = $slug;
                $this->vars['content'] = $content;
            }

        } else if ($title || $slug || $content) {
            $this->vars['error'] = '<p class="text-danger fw-bold">Fill all fields</p>';
            $this->vars['title'] = $title;
            $this->vars['slug'] = $slug;
            $this->vars['content'] = $content;

        } else {

            $this->vars['img'] = get_headers('https://picsum.photos/600/300', 1)['location'];
        }
        
    }
}