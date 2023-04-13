#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "logger");

while (true) {
    echo "Enter message: Validated or Fail('exit' to quit): ";
    $input = trim(fgets(STDIN));

    if ($input == 'exit') {
        break;
    }

    $request = array(
        'type' => 'user_input',
        'input' => $input,
    );

    $response = $client->send_request($request);

    switch ($response) {
        case 'Message processed':
            echo "Message processed successfully\n";
            break;

        case 'ERROR: unsupported message type':
            echo "Error: unsupported message type\n";
            break;

        default:
            echo "Error: invalid response from server\n";
            break;
    }
}

echo "Exiting...\n";
