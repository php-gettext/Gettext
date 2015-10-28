<?php

namespace Gettext\Generators;

use Gettext\Translations;

interface GeneratorInterface
{
    /**
     * Saves the translations in a file.
     *
     * @param Translations $translations
     * @param string       $file
     *
     * @return bool
     */
    public static function toFile(Translations $translations, $file);

    /**
     * Generates a string with the translations ready to save in a file.
     *
     * @param Translations $translations
     *
     * @return string
     */
    public static function toString(Translations $translations);
}
