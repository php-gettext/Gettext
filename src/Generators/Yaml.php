<?php

namespace Gettext\Generators;

use Gettext\Translations;
use Gettext\Utils\MultidimensionalArrayTrait;
use Symfony\Component\Yaml\Yaml as YamlDumper;

class Yaml extends Generator implements GeneratorInterface
{
    use MultidimensionalArrayTrait;

    public static $options = [
        'includeHeaders' => true,
        'inline' => 3,
        'indent' => 2,
    ];

    /**
     * {@inheritdoc}
     */
    public static function toString(Translations $translations, array $options = [])
    {
        $options += static::$options;

        return YamlDumper::dump(static::toArray($translations, $options['includeHeaders']), $options['inline'], $options['indent']);
    }
}
