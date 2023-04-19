#!/usr/bin/php
<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$packageNumberFile = '/home/branit490/workspace/packages/last_package_number.txt';
$zipCountFile = '/home/branit490/workspace/packages/zip_count.txt';
$packageDir = '/home/branit490/workspace/packages';

// get the last package number and zip count from the files
$packageNumber = intval(file_get_contents($packageNumberFile));
$zipCount = intval(file_get_contents($zipCountFile));

echo "Package Producer START".PHP_EOL;

do {
    $input = readline("Ready to create a package? (y/n): ");
} while ($input !== 'y' && $input !== 'n');

if ($input === 'y') {
    // create zip file with package number
    $zipFilePath = "$packageDir/package$packageNumber.zip";
    $cmd = "cd /home/branit490/workspace/IT490GroupRepo && zip -r $zipFilePath *";
    shell_exec($cmd);

    do {
        $input = readline("Package created. Ready to send package? (y/n): ");
    } while ($input !== 'y' && $input !== 'n');

    if ($input === 'y') {
        // send the package to the consumer
        $client = new rabbitMQClient("testRabbitMQ.ini", "logger");
        $request = array(
            'type' => 'package',
            'package_path' => $zipFilePath,
            'destination_path' => '/home/branit490/workspace/test_packages/'
        );
        $response = $client->send_request($request);

        // check if the package was saved successfully
        if ($response == "Package saved") {
            // increment package number and zip count
            $packageNumber++;
            $zipCount++;

            // update the package number and zip count files
            file_put_contents($packageNumberFile, $packageNumber);
            file_put_contents($zipCountFile, $zipCount);

            echo "Package saved as $zipFilePath".PHP_EOL;
            echo "Total number of zip files created: $zipCount".PHP_EOL;
        } else {
            echo "Failed to save package".PHP_EOL;
        }
    }
}

exit();
