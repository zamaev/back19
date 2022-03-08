<?php
require_once 'core/tools/errors.php';
require_once 'core/tools/debug.php';
require_once 'core/tools/cache.php';

require_once 'core/patterns/singleton.php';

require_once 'core/model/model.php';
require_once 'core/model/query.php';
require_once 'core/model/entity.php';

require_once 'core/libs/smarty/Smarty.class.php';
require_once 'core/routing/View.php';
require_once 'core/routing/Router.php';

// require_once 'core/auth/auth.php';

const APP_DIR = __DIR__;

Router::route();
