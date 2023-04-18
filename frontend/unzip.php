#!/usr/bin/php
<?php
/*
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
*/
$fileName = readline("Enter zip file name: ");
$folderName = substr($fileName, 0, -4);
$zipFilePath = '/home/benbandila/' . $fileName;
$destination = '/home/benbandila/test_packages/' . $folderName;
$cmd = "unzip $zipFilePath -d $destination";
shell_exec($cmd);
$cmd1 = "cd $destination && cd frontend/";
shell_exec($cmd1);
shell_exec("sh ben_copy.sh");

/*
function doLogin($username,$password)
{
    // lookup username in databas
    // check password
    return true;
    //return false if not valid
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "login":
      return doLogin($request['username'],$request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;*/
exit();
?>

