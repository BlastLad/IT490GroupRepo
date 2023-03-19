<?php
require_once("frontend/path.inc");
require_once('frontend/get_host_info.inc');
require_once('frontend/rabbitMQLib.inc');

$server = new rabbitMQServer("testRabbitMQ.ini", "logger");
echo "testRabbitMQServer LOGGER BEGIN".PHP_EOL;
$server->process_requests("processLog");



function processLog($request) {
	$logText = fopen('dist_event_logger.txt', 'a+');
	$typeText = $request['type'];
	$messageText = $request['message'];
	$newMessage ="Type of Event: $typeText Message From Event: $messageText";

	$timeOfRecieved = time();

	fwrite($logText, "$newMessage AT $timeOfRecieved".PHP_EOL);
	fclose($logText);
}



?>
