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
    $input = readline("Ready to send package? (y/n): ");
} while ($input !== 'y' && $input !== 'n');

if ($input === 'y') {
    // create zip file with package number
    $zipFilePath = "$packageDir/package$packageNumber.zip";
    $cmd = "cd /home/branit490/workspace/IT490GroupRepo && zip -r $zipFilePath *";
    shell_exec($cmd);

    // increment package number and zip count
    $packageNumber++;
    $zipCount++;

    // update the package number and zip count files
    file_put_contents($packageNumberFile, $packageNumber);
    file_put_contents($zipCountFile, $zipCount);
    $newPackageNumber = intval(file_get_contents($packageNumberFile));

    $packagePath = "/home/branit490/workspace/packages/package" .$newPackageNumber. ".zip";

    echo "Package saved as $zipFilePath".PHP_EOL;
    echo "Total number of zip files created: $zipCount".PHP_EOL;

    // send package path to consumer
    $request = array();
    $request['type'] = "package_path";
    $request['path'] = $packagePath;

    $client = new rabbitMQClient("testRabbitMQ.ini", "logger");
    echo "before sending request";
    $response = $client->send_request($request);
    echo "sent request";
}
exit();

