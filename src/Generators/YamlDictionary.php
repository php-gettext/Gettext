<?php

namespace Gettext\Generators;

use Gettext\Translations;
use Symfony\Component\Yaml\Yaml;

class YamlDictionary extends Generator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function toString(Translations $translations)
    {
        $messages = [];

        foreach ($translations as $translation) {
            $messages[$translation->getOriginal()] = $translation->getTranslation();
        }

        return Yaml::dump($messages);
    }
}
