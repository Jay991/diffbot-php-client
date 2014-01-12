<?php

require_once 'diffbot.class.php';

  
$d = new diffbot("6503df913aed4fc8d713150018d91c2c");

$c= $d->analyze("http://diffbot.com/products/automatic/image/" );

var_dump($c);
