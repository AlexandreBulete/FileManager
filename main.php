<?php

require 'vendor/autoload.php';

use App\OrderFile;


// $handle = fopen('TARIFS21032713363699.csv', 'r');
$handle = fopen('data/test.csv', 'r');

$class = new OrderFile();

$data = $class->createArray($handle);
$class->orderBySkus($data);