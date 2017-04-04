<?php

// import the DnifLogger and Consumers_BlockingHttpConsumer classes
require_once("../lib/Consumers.php");
require_once("../lib/DnifLogger.php");

$dlog = new DnifLogger(
    new Consumers_SocketConsumer("UDP_IP", UDP_PORT));

// data is a string
$data = "Hello World";

$dlog->log($data);
