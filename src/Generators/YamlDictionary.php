<?php

namespace Gettext\Generators;

use Gettext\Translations;
use Gettext\Utils\DictionaryTrait;
use Symfony\Component\Yaml\Yaml as YamlDumper;

class YamlDictionary extends Generator implements GeneratorInterface
{
    use DictionaryTrait;

    public static $options = [
        'inline' => 3,
        'indent' => 2,
        'includeHeaders' => false,
    ];

    /**
     * {@inheritdoc}
     */
    public static function toString(Translations $translations, array $options = [])
    {
        $options += static::$options;

        return YamlDumper::dump(self::toArray($translations, $options['includeHeaders']), $options['inline'], $options['indent']);
    }
}
