#!/usr/bin/php
<?php

$fileName = readline("Enter zip file name: ");
$folderName = substr($fileName, 0, -4);
$zipFilePath = '/home/benbandila/' . $fileName;
$destination = '/home/benbandila/test_packages/' . $folderName;
$cmd = "unzip $zipFilePath -d $destination";
shell_exec($cmd);
$cmd1 = "cd $destination && cd frontend/";
shell_exec($cmd1);
shell_exec("sh ben_copy.sh");
exit();
?>

