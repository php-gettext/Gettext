<?php

namespace Gettext\Generators;

use Gettext\Translations;

class JsonDictionary extends Generator implements GeneratorInterface
{
    public static $options = JSON_PRETTY_PRINT;

    /**
     * {@parentDoc}.
     */
    public static function toString(Translations $translations)
    {
        $messages = [];

        foreach ($translations as $translation) {
            $messages[$translation->getOriginal()] = $translation->getTranslation();
        }

        return json_encode($messages, static::$options);
    }
}
