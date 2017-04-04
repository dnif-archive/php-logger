<?php

require_once("../lib/Consumers.php");
require_once("../lib/DnifLogger.php");

$dlog = new DnifLogger(
    new Consumers_BlockingHttpConsumer("http://202.87.34.253:9234/json/receive"));

// data is a *sequential array* of key value pairs
$data = array(
    array("key1" => "value1")
);

$dlog->log($data);
