<?php

namespace Gettext\Generators;

use Gettext\Translations;

class Jed extends PhpArray implements GeneratorInterface
{
    /**
     * {@parentDoc}.
     */
    public static function toString(Translations $translations)
    {
        $array = static::toArray($translations);

        return json_encode($array);
    }

    /**
     * {@parentdoc}.
     */
    protected static function buildArray(Translations $translations)
    {
        $array = array();

        $context_glue = "\004";

        foreach ($translations as $translation) {
            $key = ($translation->hasContext() ? $translation->getContext().$context_glue : '').$translation->getOriginal();

            if ($translation->hasPluralTranslation()) {
                $array[$key] = array_merge(array($translation->getTranslation()), $translation->getPluralTranslation());
            } else {
                $array[$key] = array($translation->getTranslation());
            }
        }

        return $array;
    }
}
