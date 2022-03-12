<?php

use \Core\Controller;

class BlogPostAdd extends Controller
{
    public function run()
    {
        $this->assign('title', 'Add post');
        $this->assign('menu_item', 'blog');
        
        $post_title = $_POST['post']['title'] ?? null;
        $post_slug = $_POST['post']['slug'] ?? null;
        $post_content = $_POST['post']['content'] ?? null;
        $post_thumb = $_POST['post']['thumb'] ?? get_headers('https://picsum.photos/600/300', 1)['location'];
        $post_category_id = $_POST['post']['category__id'] ?? null;
        
        $error = '';
        // так же сделать потом валидацию формы

        if ($post_title && $post_slug && $post_content && $post_category_id) {

            if (!model()->posts->where(['post.slug' => $post_slug])->fetch()) {
                $new_post = model()->post();
                $post_id = $new_post->setData($_POST['post'])->save()->data()['post'];
                $post = model()->posts->categories->where(['post' => $post_id])->fetch();
                $category_slug = $post['category.slug'];
                $post_slug = $post['post.slug'];
                header("Location: /blog/{$category_slug}/{$post_slug}/");
            } else {
                $error = '<p class="text-danger fw-bold">This slug is exist</p>';
            }

        } else if (isset($_POST['post'])) {
            $error = '<p class="text-danger fw-bold">Fill all fields</p>';
        }

        $categories = model()->categories->fetchAll();


        $this->assign('post_title', $post_title);
        $this->assign('post_slug', $post_slug);
        $this->assign('post_content', $post_content);
        $this->assign('post_thumb', $post_thumb);
        $this->assign('post_category_id', $post_category_id);
        $this->assign('categories', $categories);
        $this->assign('error', $error);
    }
}