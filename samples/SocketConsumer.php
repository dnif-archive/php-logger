<?php

// import the DnifLogger and Consumers_BlockingHttpConsumer classes
require_once("../lib/Consumers.php");
require_once("../lib/DnifLogger.php");

$dlog = new DnifLogger(
    new Consumers_SocketConsumer("202.87.34.253", 9234));

// data is a string
$data = "Hello World";

$dlog->log($data);
