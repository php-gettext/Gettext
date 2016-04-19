<?php

error_reporting(E_ALL);

$path = dirname(__DIR__);

include_once $path.'/vendor/autoload.php';
include_once $path.'/tests/AbstractTest.php';

PHPUnit_Framework_Error_Notice::$enabled = true;
