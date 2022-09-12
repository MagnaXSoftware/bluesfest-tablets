<?php

require __DIR__ . '/../vendor/autoload.php';

session_start();

$app = \App\Bootstrap::app();

$app->run();