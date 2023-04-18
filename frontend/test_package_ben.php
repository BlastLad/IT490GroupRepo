#!/usr/bin/php
<?php
//UPDATED VERSION - USES SCP

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$packageDir = '/home/benbandila/test_packages';

echo "Package Producer START".PHP_EOL;

do {
    $input = readline("Ready to send package? (y/n): ");
} while ($input !== 'y' && $input !== 'n');

if ($input === 'y') {
    // prompt the user for the package name
    $packageName = readline("Enter package name: ");

    // TODO change this to select the existing zip file
    $zipFilePath = "$packageDir/$packageName.zip";
    //$cmd = "cd /home/benbandila/IT490GroupRepo && zip -r $zipFilePath *"; //unnecessary?
    //shell_exec($cmd);

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
            exit();
        }
    } elseif ($input === 'd') {
        // send the package to the consumer using scp
        $destination = 'branit490@192.168.192.165:/home/branit490/workspace/'; //change to Brandon's info
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
            exit();
        }
    }
    
}

exit();