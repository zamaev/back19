<?php
require_once 'core/tools/errors.php';
require_once 'core/tools/debug.php';
require_once 'core/tools/cache.php';

require_once 'core/patterns/singleton.php';

require_once 'core/model/model.php';
require_once 'core/model/query.php';
require_once 'core/model/entity.php';

require_once 'core/routing/page.php';
require_once 'core/routing/router.php';

// require_once 'core/auth/auth.php';


Router::route();

// debug(model()->users->order('name', 'desc')->fetchAll());

// model()->users->update(1, ['job' => 3]);

// debug(model()->users->inner('jobs')->fetchAll());

// debug(model()->posts->categories->fetchAll());


// debug(model()->query($query)->fetchAll());
// debug(model()->query('SELECT * FROM posts LEFT JOIN categories ON posts.category__id = categories.category')->fetchAll());

// debug(model()->users->jobs->fetchAll());
// debug(model()->posts->categories->fetchAll());
// debug(model()->posts->categories->users->fetchAll());
// debug(model()->posts->categories->users->jobs->fetchAll());

// debug(model()->posts->categories->where(['category.slug' => 'winter'])->fetchAll());
debug(model()->posts->categories->users->jobs->where(['category.slug' => 'winter'])->fetchAll());