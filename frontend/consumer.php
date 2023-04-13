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
        case "user_input":
            $input = $request['input'];
            if ($input == "Validated") {
                echo "Package has been validated... now sending to prod\n";
            } elseif ($input == "Fail") {
                echo "Package has failed... now sending back to Dev\n";
            } else {
                echo "Invalid input\n";
            }
            return "Message processed";

        default:
            return "ERROR: unsupported message type";
    }
}
