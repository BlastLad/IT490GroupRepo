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
        case "package_path":
            echo "request has been recieved";
            $packagePath = $request['path'];
            $testPackageDir = '/home/branit490/workspace/test_package'; // directory to save packages

            // copy the package to test_package directory
            $cmd = "cp -R ". $packagePath . " " . $testPackageDir;
            shell_exec($cmd);

            //echo "Package saved in $testPackageDir\n";
            return "Package saved successfully.";

        default:
            return "ERROR: unsupported message type";
    }
}
