#!/usr/bin/php
<?php
//UPDATED VERSION - USES SCP

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

//$packageNumberFile = '/home/branit490/workspace/packages/last_package_number.txt';
//$zipCountFile = '/home/branit490/workspace/packages/zip_count.txt';
$packageDir = '/home/benbandila/packages';

// get the last package number and zip count from the files
//$packageNumber = intval(file_get_contents($packageNumberFile));
//$zipCount = intval(file_get_contents($zipCountFile));

echo "Package Producer START".PHP_EOL;

do {
    $input = readline("Ready to send package? (y/n): ");
} while ($input !== 'y' && $input !== 'n');

if ($input === 'y') {
    // prompt the user for the package name
    $packageName = readline("Select the package to send: ");

    // TODO change this to select the existing zip file
    // create zip file with package name
    $zipFilePath = "$packageDir/$packageName.zip";
    $cmd = "cd /home/benbandila/IT490GroupRepo && zip -r $zipFilePath *"; //unnecessary?
    shell_exec($cmd);

    do {
        $input = readline("Package selected. Send package to prod or dev? (p/d): ");
    } while ($input !== 'p' && $input !== 'd');

    if ($input === 'p') {
        // send the package to the consumer using scp
        $destination = 'benbandila@192.168.192.247:/home/benbandila/'; //change to Sean's info
        $cmd = "scp $zipFilePath $destination";
        //shell_exec($cmd);
        
        //TODO ADD RABBITMQ DEPLOYEMENT CHECKER!
        $client = new rabbitMQClient("testRabbitMQ.ini", "logger"); //change logger to new deployment queue
        $request = array();
        $request['type'] = "prod";
        $request['name'] = $packageName;
        $response = $client->send_request($request);
        if ($response['val'] == '1'){
            shell_exec($cmd);
            echo "Package sent to prod!";
        } else {
            echo "Something went wrong. Try again";
        }
        
/*
        // check if the package was saved successfully
        if (file_exists("$destination/$packageName.zip")) {
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
        */
    } elseif ($input === 'd') {
        // send the package to the consumer using scp
        $destination = 'branit490@192.168.192.165:/home/benbandila/'; //change to Brandon's info
        $cmd = "scp $zipFilePath $destination";
        //shell_exec($cmd);
        
        //TODO ADD RABBITMQ DEPLOYEMENT CHECKER!
        $client = new rabbitMQClient("testRabbitMQ.ini", "logger"); //change logger to deployment queue
        $request = array();
        $request['type'] = "dev";
        $request['name'] = $packageName;
        $response = $client->send_request($request);
        if ($response['val'] == '0'){
            shell_exec($cmd);
            echo "Package sent back to dev!";
        } else {
            echo "Something went wrong. Try again";
        }
    }
    
}

exit();