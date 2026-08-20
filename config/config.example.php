<?php
/**
 * pondokhuda api configuration — TEMPLATE (safe to commit).
 *
 * Copy to config/config.local.php (dev box) OR api/config-db.php (cPanel),
 * fill in real values, and NEVER commit the filled copy.
 *
 * Load order in api/kon.php:
 *   1. __DIR__ . '/../config/config.local.php'
 *   2. __DIR__ . '/config-db.php'
 *   3. env vars (DB_HOST, DB_USER, DB_PASS, DB_NAME, PH_API_TOKEN)
 */
return array(
    'db' => array(
        'host' => 'localhost',
        'user' => 'db_user',
        'pass' => 'db_password',
        'name' => 'db_name',
    ),
    'tokens' => array(
        // one or more accepted X-Api-Token values for gated endpoints
        'change-me-token',
    ),
);