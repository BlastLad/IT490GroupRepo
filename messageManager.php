<?php
require_once('frontend/path.inc');
require_once('frontend/get_host_info.inc');
require_once('frontend/rabbitMQLib.inc');


function directMessage(array $body, string $queueToUse) {

	$client = new rabbitMQClient("testRabbitMQ.ini", $queueToUse);
	$response = $client->send_request($body);
	return $response;
}
?>
