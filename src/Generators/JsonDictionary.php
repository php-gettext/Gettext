<?php

namespace Gettext\Generators;

use Gettext\Translations;
use Gettext\Utils\DictionaryTrait;

class JsonDictionary extends Generator implements GeneratorInterface
{
    use DictionaryTrait;

    public static $options = [
        'json' => JSON_PRETTY_PRINT,
        'includeHeaders' => false,
    ];

    /**
     * {@parentDoc}.
     */
    public static function toString(Translations $translations, array $options = [])
    {
        $options += static::$options;

        return json_encode(self::toArray($translations, $options['includeHeaders']), $options['json']);
    }
}
