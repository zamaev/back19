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


Router::route();

// model();

// model('jobs')->insert(['major'=>'lksfj', 'salary'=>'73333']);
// debug($query);
// $db = model()->db;
// $result = $db->query($query);
// var_dump($result);
// debug($db->query($query)->fetch_all(MYSQLI_ASSOC));

// require_once 'core/classes/session.php';
// require_once 'core/auth/auth.php';
