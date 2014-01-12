<?php

require_once 'diffbot.class.php';

  
$d = new diffbot("DEVELOPER_TOKEN");

$c= $d->analyze("http://diffbot.com/products/" );

var_dump($c);
