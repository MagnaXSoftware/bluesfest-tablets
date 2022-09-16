<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Bootstrap;

session_start();

$app = Bootstrap::app();

$app->run();