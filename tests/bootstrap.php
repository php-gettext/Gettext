<?php
error_reporting(E_ALL);

$path = dirname(__DIR__);

if (is_file($path.'/vendor/autoload.php')) {
	include_once $path.'/vendor/autoload.php';
} elseif (is_file($path.'/../../vendor/autoload.php')) {
	include_once $path.'/../../vendor/autoload.php';
} else {
	throw new \Exception("Composer autoloader not found! ($path)");
}

PHPUnit_Framework_Error_Notice::$enabled = true;
