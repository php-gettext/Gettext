<?php

namespace Gettext\Generators;

use Gettext\Translations;
use Gettext\Utils\DictionaryTrait;
use Symfony\Component\Yaml\Yaml as YamlDumper;

class Yaml extends Generator implements GeneratorInterface
{
    public static $options = [
        'inline' => 3,
        'indent' => 2,
    ];

    use DictionaryTrait;

    /**
     * {@inheritdoc}
     */
    public static function toString(Translations $translations, array $options = [])
    {
        $options += static::$options;

        $messages = [];

        foreach ($translations as $translation) {
            $context = $translation->getContext();
            $original = $translation->getOriginal();

            if (!isset($messages[$context])) {
                $messages[$context] = [];
            }

            if ($translation->hasPluralTranslations()) {
                $messages[$context][$original] = $translation->getPluralTranslations();
                array_unshift($messages[$context][$original], $translation->getTranslation());
            } else {
                $messages[$context][$original] = $translation->getTranslation();
            }
        }

        return YamlDumper::dump($messages, $options['inline'], $options['indent']);
    }
}
