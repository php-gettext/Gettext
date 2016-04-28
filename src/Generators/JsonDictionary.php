<?php

namespace Gettext\Generators;

use Gettext\Translations;
use Gettext\Utils\DictionaryTrait;

class JsonDictionary extends Generator implements GeneratorInterface
{
    public static $options = JSON_PRETTY_PRINT;

    use DictionaryTrait;

    /**
     * {@parentDoc}.
     */
    public static function toString(Translations $translations)
    {
        return json_encode(self::toArray($translations), static::$options);
    }
}
