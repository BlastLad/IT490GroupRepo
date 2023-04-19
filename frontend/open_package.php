#!/usr/bin/php
<?php

$packageDir = '/home/branit490/workspace/test_packages';
$destinationDir = '/home/branit490/workspace/Test490';

echo "Package Consumer START".PHP_EOL;

// get the most recent package file in the directory
$latestPackage = '';
$latestTime = 0;

$dir = scandir($packageDir);
foreach ($dir as $file) {
    if (strpos($file, '.zip') !== false) {
        $filePath = $packageDir . '/' . $file;
        $modifiedTime = filemtime($filePath);
        if ($modifiedTime > $latestTime) {
            $latestPackage = $filePath;
            $latestTime = $modifiedTime;
        }
    }
}

if (empty($latestPackage)) {
    echo "No packages found".PHP_EOL;
    exit();
}

echo "Latest package: ".$latestPackage.PHP_EOL;

// prompt user to copy package
echo "Do you want to copy the latest package to $destinationDir? (y/n) ";
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
if(trim($line) != 'y'){
    echo "Aborted".PHP_EOL;
    exit();
}
fclose($handle);

// unzip package and copy files to destination directory
$zip = new ZipArchive;
$res = $zip->open($latestPackage);
if ($res === TRUE) {
    $zip->extractTo($destinationDir);
    $zip->close();
    echo "Package contents copied to $destinationDir".PHP_EOL;

    // run copy.sh
    exec("cd $destinationDir/frontend && ./copy.sh");
    echo "copy.sh complete".PHP_EOL;

    // restart apache
    exec("sudo service apache2 restart");
    echo "Apache restarted".PHP_EOL;

} else {
    echo "Failed to unzip package".PHP_EOL;
}
