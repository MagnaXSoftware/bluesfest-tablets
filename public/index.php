<?php

require __DIR__ . '/../vendor/autoload.php';

session_start();

$app = \App\Boostrap::app();

$app->run();