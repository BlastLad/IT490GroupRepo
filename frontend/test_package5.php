#!/usr/bin/php
<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$packageDir = '/home/branit490/workspace/packages';

echo "Package Producer START".PHP_EOL;

do {
    $input = readline("Ready to create a package? (y/n): ");
} while ($input !== 'y' && $input !== 'n');

if ($input === 'y') {
    // prompt the user for the package name
    $packageName = readline("Enter the package name: ");

    // create zip file with package name
    $zipFilePath = "$packageDir/$packageName.zip";
    $cmd = "cd /home/branit490/workspace/IT490GroupRepo/frontend && zip -r $zipFilePath *";
    shell_exec($cmd);

    do {
        $input = readline("Package created. Ready to send package? (y/n): ");
    } while ($input !== 'y' && $input !== 'n');

    if ($input === 'y') {
        // send the package to the consumer using scp
        $destination = 'benbandila@192.168.192.247:/home/benbandila/';
        $cmd = "scp $zipFilePath $destination";
        shell_exec($cmd);

        // establish connection to RabbitMQ server
        $server = new rabbitMQClient("testRabbitMQ.ini", "deployment");

        // create message to send to consumer
        $msg = array(
            "type" => "package",
            "name" => $packageName
        );

        // send message to consumer
        $response = $server->send_request($msg);

        if ($response["returncode"] == '0') {
            echo "Package sent!";
        } else {
            echo "Failed to send package";
        }
    }
}

exit();
