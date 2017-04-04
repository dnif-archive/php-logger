<?php

// import the DnifLogger and Consumers_BlockingHttpConsumer classes
require_once("../lib/Consumers.php");
require_once("../lib/DnifLogger.php");

$dlog = new DnifLogger(
    new Consumers_BlockingHttpConsumer("http://TARGET_IP:PORT/json/receive"));

// data is a *sequential array* of key value pairs
$data = array(
    array("key1" => "value1")
);

// Warning: This will block until the request is complete
$dlog->log($data);
