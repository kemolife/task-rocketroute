<?php
use App\Application;

$loader = require __DIR__ . '/vendor/autoload.php';
$config = require __DIR__ . '/config/main.php';

(new Application($config))->run();