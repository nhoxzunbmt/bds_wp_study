<?php
/** Development */
define('SAVEQUERIES', true);
define('WP_DEBUG', true);
define('SCRIPT_DEBUG', true);


/** Redis */
define('WP_REDIS_HOST', '192.168.9.34');
define('WP_REDIS_PORT', '6379');
define('WP_CACHE_KEY_SALT', 'bds_');
define('WP_REDIS_DISABLED', false);
define('WP_REDIS_SELECTIVE_FLUSH', true);
define('WP_DEBUG_LOG',true);