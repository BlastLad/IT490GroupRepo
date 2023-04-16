#!/usr/bin/php
<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$packageDir = '/home/branit490/workspace/test_packages';

echo "Package Consumer START".PHP_EOL;

function process_package($path, $destination_path)
{
    $filename = basename($path);

    // check if package directory exists, create it if it doesn't
    if (!file_exists($destination_path)) {
        mkdir($destination_path, 0777, true);
    }

    // check if the package file already exists
    if (file_exists("$destination_path/$filename")) {
        echo "Package already exists".PHP_EOL;
        return "Package already exists";
    }

    // move the package to the destination directory
    if (copy($path, "$destination_path/$filename")) {
        echo "Package saved".PHP_EOL;
        return "Package saved";
    } else {
        echo "Failed to save package".PHP_EOL;
        return "Failed to save package";
    }
}

function request_processor($request)
{
    echo "Received Request".PHP_EOL;
    var_dump($request);

    if (!isset($request['type'])) {
        return "ERROR: Unsupported message type";
    }

    switch ($request['type']) {
        case 'package':
            return process_package($request['package_path'], $request['destination_path']);
        default:
            return "ERROR: Unsupported message type";
    }
}

$server = new rabbitMQServer("testRabbitMQ.ini", "logger");
$server->process_requests('request_processor');

exit();
