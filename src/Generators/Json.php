<?php

namespace Gettext\Generators;

use Gettext\Translations;
use Gettext\Utils\MultidimensionalArrayTrait;

class Json extends Generator implements GeneratorInterface
{
    use MultidimensionalArrayTrait;

    public static $options = [
        'json' => JSON_PRETTY_PRINT,
        'includeHeaders' => true,
    ];

    /**
     * {@inheritdoc}
     */
    public static function toString(Translations $translations, array $options = [])
    {
        $options += static::$options;

        return json_encode(self::toArray($translations, $options['includeHeaders'], true), $options['json']);
    }
}
