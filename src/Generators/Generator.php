<?php

namespace Gettext\Generators;

use Gettext\Translation;
use Gettext\Translations;

abstract class Generator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function toFile(Translations $translations, $file, array $options = [])
    {
        array_walk($translations, '\Gettext\Generators\Generator::filterTranslation');

        $content = static::toString($translations, $options);

        if (file_put_contents($file, $content) === false) {
            return false;
        }

        return true;
    }

    /**
     * Filters a translation before saving it to a file.
     *
     * @param \Gettext\Translation $translation
     * @return \Gettext\Translation
     */
    public static function filterTranslation(Translation $translation)
    {
        if (Translations::$options['normalizeLineBreaks'] !== false) {
            $text = $translation->getTranslation();

            $text = preg_replace("/\n\r?/", Translations::$options['normalizeLineBreaks'], $text);

            $translation->setTranslation($text);
        }

        return $translation;
    }
}
