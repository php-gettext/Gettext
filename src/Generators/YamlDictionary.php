<?php

namespace Gettext\Generators;

use Gettext\Translations;
use Gettext\Utils\DictionaryTrait;
use Symfony\Component\Yaml\Yaml;

class YamlDictionary extends Generator implements GeneratorInterface
{
    use DictionaryTrait;

    /**
     * {@inheritdoc}
     */
    public static function toString(Translations $translations)
    {
        return Yaml::dump(self::toArray($translations));
    }
}
