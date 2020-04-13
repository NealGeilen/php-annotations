<?php

use Router\Router;

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/Controllers/Test.php";
require __DIR__ . "/Controllers/Test4.php";
require __DIR__ . "/Controllers/Test/Test4.php";

Router::setControllerDirectorie(__DIR__ . DIRECTORY_SEPARATOR . "Controllers");
Router::displayPage("/testawkdawdjlawkdjlawkdjawldjawlkdjalkwjdlkawjjlawkjd");


