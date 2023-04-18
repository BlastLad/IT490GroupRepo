#!/usr/bin/php
<?php

$fileName = readline("Enter zip file name: ");
$folderName = substr($fileName, 0, -4);
$zipFilePath = '/home/benbandila/' . $fileName;
$destination = '/home/benbandila/test_packages/' . $folderName;
$cmd = "unzip $zipFilePath -d $destination";
shell_exec($cmd);
$cmd1 = "cd $destination";
shell_exec($cmd1);
$string = "for file in ~/test_packages/$folderName/*;";
$copyFile = "$destination/ben_copy.sh";
$lines = file($copyFile);
$lines[2] = $string . PHP_EOL;
file_put_contents($copyFile, implode('', $lines));
shell_exec("sh $copyFile");
//shell_exec("sudo service apache2 restart");
exit();
?>

