<?php

error_reporting(E_ALL);

include_once dirname(__DIR__).'/vendor/autoload.php';

PHPUnit_Framework_Error_Notice::$enabled = true;

//Config
Gettext\Translations::$options['defaultDateHeaders'] = [];
Gettext\Generators\Jed::$options['json'] = JSON_PRETTY_PRINT;
Gettext\Generators\Json::$options['json'] = JSON_PRETTY_PRINT;
Gettext\Generators\JsonDictionary::$options['json'] = JSON_PRETTY_PRINT;
