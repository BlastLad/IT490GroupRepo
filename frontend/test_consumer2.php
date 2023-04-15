#!/usr/bin/php
<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$server = new rabbitMQServer("testRabbitMQ.ini", "logger");

echo "Test RabbitMQ Server BEGIN".PHP_EOL;
$server->process_requests('processMessage');
echo "Test RabbitMQ Server END".PHP_EOL;
exit();

function processMessage($request) {
    if (!isset($request['type'])) {
        return "ERROR: unsupported message type";
    }

    switch ($request['type']) {
        case "package":
            $packagePath = $request['package_path'];
            $zipFilePath = "$packagePath.zip";

            // create test_packages directory if it does not exist
            if (!file_exists('/home/branit490/workspace/test_packages')) {
                mkdir('/home/branit490/workspace/test_packages');
            }

            // copy zip file to test_packages directory
            copy($zipFilePath, "/home/branit490/workspace/test_packages/$zipFilePath");

            echo "Package saved as /home/branit490/workspace/test_packages/$zipFilePath" . PHP_EOL;

            return "Package saved successfully";

        default:
            return "ERROR: unsupported message type";
    }
}
