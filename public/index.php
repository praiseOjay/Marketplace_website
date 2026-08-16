<?php

use App\Kernel;

if (isset($_SERVER['LAMBDA_TASK_ROOT']) || getenv('LAMBDA_TASK_ROOT')) {
    $bundledDb = dirname(__DIR__) . '/var/data.db';
    $tmpDb = '/tmp/data.db';
    if (file_exists($bundledDb)) {
        if (!file_exists($tmpDb) || filesize($tmpDb) < filesize($bundledDb)) {
            @copy($bundledDb, $tmpDb);
        }
    }
}

// Serve static assets directly if requested file exists in public directory
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$filePath = __DIR__ . $requestPath;

if ($requestPath !== '/' && $requestPath !== '/index.php' && is_file($filePath)) {
    if (php_sapi_name() === 'cli-server') {
        return false;
    }

    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'css'   => 'text/css; charset=UTF-8',
        'js'    => 'application/javascript; charset=UTF-8',
        'json'  => 'application/json; charset=UTF-8',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'webp'  => 'image/webp',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'eot'   => 'application/vnd.ms-fontobject',
        'otf'   => 'font/otf',
        'map'   => 'application/json',
    ];

    $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
    header('Content-Type: ' . $mimeType);
    header('Cache-Control: public, max-age=31536000, immutable');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
}

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
