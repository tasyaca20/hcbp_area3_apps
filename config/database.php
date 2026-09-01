<?php

use Illuminate\Support\Str;
use Pdo\Mysql;

return [

    'default' => env('DB_CONNECTION', env('APP_ENV') === 'production' ? 'mysql' : 'sqlite'),

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => database_path('database.sqlite'),
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],

        'mysql' => [
            'driver' => 'mysql',
            // Do not use DB_URL here: a stale/auto-generated URL can override
            // the Railway TCP proxy host/port supplied by DB_HOST/DB_PORT.
            'driver' => 'mysql',
            'host' => env('DB_HOST', env('MYSQLHOST', 'sakura.proxy.rlwy.net')),
            'port' => env('DB_PORT', env('MYSQLPORT', '10132')),
            'database' => env('DB_DATABASE', env('MYSQLDATABASE', 'railway')),
            'username' => env('DB_USERNAME', env('MYSQLUSER', 'root')),
            'password' => env('DB_PASSWORD', env('MYSQLPASSWORD', '')),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                Mysql::ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mariadb' => [
            'driver' => 'mariadb', 'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'), 'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'), 'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''), 'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci', 'prefix' => '', 'prefix_indexes' => true,
            'strict' => true, 'engine' => null,
        ],
        'pgsql' => [
            'driver' => 'pgsql', 'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'), 'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'), 'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8', 'prefix' => '', 'prefix_indexes' => true, 'search_path' => 'public',
            'sslmode' => env('DB_SSLMODE', 'prefer'),
        ],
        'sqlsrv' => [
            'driver' => 'sqlsrv', 'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'), 'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'), 'password' => env('DB_PASSWORD', ''), 'charset' => 'utf8',
        ],
    ],

    'migrations' => ['table' => 'migrations', 'update_date_on_publish' => true],

    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'options' => ['cluster' => env('REDIS_CLUSTER', 'redis'), 'prefix' => env('REDIS_PREFIX', Str::slug((string) env('APP_NAME', 'laravel')).'-database-')],
        'default' => ['url' => env('REDIS_URL'), 'host' => env('REDIS_HOST', '127.0.0.1'), 'username' => env('REDIS_USERNAME'), 'password' => env('REDIS_PASSWORD'), 'port' => env('REDIS_PORT', '6379'), 'database' => env('REDIS_DB', '0')],
        'cache' => ['url' => env('REDIS_URL'), 'host' => env('REDIS_HOST', '127.0.0.1'), 'username' => env('REDIS_USERNAME'), 'password' => env('REDIS_PASSWORD'), 'port' => env('REDIS_PORT', '6379'), 'database' => env('REDIS_CACHE_DB', '1')],
    ],
];