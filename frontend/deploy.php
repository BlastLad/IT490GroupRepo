#!/usr/bin/php
<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// establish connection to RabbitMQ server
$server = new rabbitMQServer("testRabbitMQ.ini", "logger");
echo "Deployment Consumer START".PHP_EOL;
$server->process_requests("insert_package_name");
// include database connection information



function insert_package_name($request) {
    $name = $request['name'];
    $mydb = new mysqli('127.0.0.1', 'testuser', '12345', 'deploy');

    if ($mydb->connect_error) {
        die("Connection failed: " . $mydb->connect_error);
    }

    // prepare and execute SQL statement to insert package name into packages table
    $query = "INSERT INTO packages (name) VALUES ('$name')";
    $response = $mydb->query($query);
    $query = "SELECT * FROM packages WHERE name = '$name'";
    $response = $mydb->query($query);
    if (mysqli_num_rows($response) > 0) //already present
    {
        echo "name added to database!";
    }

    /*
    // check if insertion was successful
    if ($query->affected_rows > 0) {
        echo "Package name '$name' inserted successfully".PHP_EOL;
    } else {
        echo "Failed to insert package name '$name'".PHP_EOL;
    }

    // close statement
    $query->close();
    */
    
    return array("returncode"=>"0");
}



/*
$server->process_requests(function ($request) {
    if ($request['type'] == 'package') {
        $packageName = $request['name'];
        insert_package_name($packageName);

        return 'success';
    }
});
*/ 

