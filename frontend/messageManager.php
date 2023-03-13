#!/user/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


function directMessage(array $body, string $queueToUse) {

	$client = new rabbitMQClient("testRabbitMQ.ini", $queueToUse);
	$response = $client->send_request($body);
	return $response;
}
?>
