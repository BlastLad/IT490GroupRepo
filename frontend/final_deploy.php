#!/usr/bin/php
<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// establish connection to RabbitMQ server
$server = new rabbitMQServer("testRabbitMQ.ini", "deployment");
echo "Deployment Consumer START".PHP_EOL;
$server->process_requests("deployment");
// include database connection information



function deployment($request) {
    $mydb = new mysqli('127.0.0.1', 'testuser', '12345', 'deploy');
    if ($mydb->errno != 0) {
        echo "failed to connect to database: " . $mydb->error . PHP_EOL;
        exit(0);
    }
    if(!isset($request['type']))
    {
        return "ERROR: unsupported message type";
    }
    switch ($request['type'])
    {
        case "package":
            $name = $request['name'];
            // prepare and execute SQL statement to insert package name into packages table
            $query = "INSERT INTO packages (name) VALUES ('$name')";
            $response = $mydb->query($query);
            $query = "SELECT * FROM packages WHERE name = '$name'";
            $response = $mydb->query($query);
            if (mysqli_num_rows($response) > 0) //already present
            {
                echo "name added to database!";
                return array("returncode"=>"0");
            }
        case "dev":
            $packageName = $request['name'];
            $query = "UPDATE packages SET verdict='FAIL' WHERE name='$packageName';";
            $response = $mydb->query($query);
            if ($response) {
                echo "Database updated with verdict";
                return array("val" => '0');
            }
        case "prod":
            $packageName = $request['name'];
            $query = "UPDATE packages SET verdict='PASS' WHERE name='$packageName';";
            $response = $mydb->query($query);
            if ($response) {
                echo "Database updated with verdict";
                return array("val" => '1');
            }
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

