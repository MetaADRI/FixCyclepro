<?php

/*
| Neon (neon.tech) provides two connection strings for the same database:
|
|   DATABASE_URL            -> pooled host   (PgBouncer, transaction mode)
|   DATABASE_URL_UNPOOLED   -> direct host
|
| `neon env pull` writes both. The application connection uses the *direct*
| endpoint by default: Laravel's PDO pgsql driver issues server-side prepared
| statements, which PgBouncer in transaction mode cannot keep stable, and the
| Render deployment runs long-lived processes (artisan serve + queue:work)
| rather than bursty serverless invocations.
|
| The pooled endpoint stays available as the `pgsql_pooled` connection.
| Explicit DB_* environment variables always win over the URLs, so the same
| config works unmodified on Render, where DB_* is supplied directly.
*/
$neonUrl = env('DATABASE_URL_UNPOOLED') ?: env('DATABASE_URL');

$neon = [
    'host' => null,
    'port' => null,
    'database' => null,
    'username' => null,
    'password' => null,
    'sslmode' => null,
];

if ($neonUrl) {
    $parts = parse_url($neonUrl);

    if (is_array($parts)) {
        $query = [];
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        $neon['host'] = $parts['host'] ?? null;
        $neon['port'] = $parts['port'] ?? null;
        $neon['database'] = isset($parts['path']) ? ltrim($parts['path'], '/') : null;
        $neon['username'] = isset($parts['user']) ? rawurldecode($parts['user']) : null;
        $neon['password'] = isset($parts['pass']) ? rawurldecode($parts['pass']) : null;
        $neon['sslmode'] = $query['sslmode'] ?? null;
    }
}

/*
| Host/port of the pooled endpoint, for the `pgsql_pooled` connection below.
| Derived from DATABASE_URL only; never used as the default connection.
*/
$neonPooledHost = null;
$neonPooledPort = null;

if (env('DATABASE_URL')) {
    $pooledParts = parse_url(env('DATABASE_URL'));

    if (is_array($pooledParts)) {
        $neonPooledHost = $pooledParts['host'] ?? null;
        $neonPooledPort = $pooledParts['port'] ?? null;
    }
}

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'pgsql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'mysql2' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => '',
            'username' => '',
            'password' => '',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST') ?: ($neon['host'] ?: '127.0.0.1'),
            'port' => env('DB_PORT') ?: ($neon['port'] ?: '5432'),
            'database' => env('DB_DATABASE') ?: ($neon['database'] ?: 'forge'),
            'username' => env('DB_USERNAME') ?: ($neon['username'] ?: 'forge'),
            'password' => env('DB_PASSWORD') !== null && env('DB_PASSWORD') !== '' ? env('DB_PASSWORD') : ($neon['password'] ?: ''),
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => env('DB_SSLMODE') ?: ($neon['sslmode'] ?: 'prefer'),
        ],

        /*
        | Same Neon database reached through the PgBouncer pooled endpoint
        | (DATABASE_URL). Not the default: see the note at the top of this file.
        | Use for bursty, connection-per-request traffic if the direct endpoint
        | ever exhausts connection slots.
        */
        'pgsql_pooled' => [
            'driver' => 'pgsql',
            'host' => env('DB_POOLED_HOST') ?: ($neonPooledHost ?: '127.0.0.1'),
            'port' => env('DB_POOLED_PORT') ?: ($neonPooledPort ?: '5432'),
            'database' => env('DB_DATABASE') ?: ($neon['database'] ?: 'forge'),
            'username' => env('DB_USERNAME') ?: ($neon['username'] ?: 'forge'),
            'password' => env('DB_PASSWORD') !== null && env('DB_PASSWORD') !== '' ? env('DB_PASSWORD') : ($neon['password'] ?: ''),
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => env('DB_SSLMODE') ?: ($neon['sslmode'] ?: 'require'),
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],
    'redis_for_config' => env('REDIS_FOR_CONFIG', false),

];