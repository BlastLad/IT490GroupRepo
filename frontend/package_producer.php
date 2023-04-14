#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$packageNumber = 1; // initialize package number to 1

echo "Package Producer START".PHP_EOL;
$packageDir = '/home/branit490/workspace/packages'; // directory to save packages

do {
    $input = readline("Ready to send package? (y/n): ");
} while ($input !== 'y' && $input !== 'n');

if ($input === 'y') {
    // create new package folder
    $packagePath = "$packageDir/package$packageNumber";
    mkdir($packagePath);

    // copy all files from IT490GroupRepo to package folder
    $cmd = "cp -R /home/branit490/workspace/IT490GroupRepo/* $packagePath";
    shell_exec($cmd);

    echo "Package saved in $packagePath".PHP_EOL;

    // increment package number
    $packageNumber++;
}

exit();
