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
    public static function toString(Translations $translations, array $options = [])
    {
        return Yaml::dump(self::toArray($translations));
    }
}
