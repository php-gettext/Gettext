<?php
namespace Gettext\Generators;

use Gettext\Translations;

abstract class Generator
{
    /**
     * Saves the translations in a file
     *
     * @param Translations $translations
     * @param string       $file
     *
     * @return boolean
     */
    public static function toFile(Translations $translations, $file)
    {
        $content = static::toString($translations);

        if (file_put_contents($file, $content) === false) {
            return false;
        }

        return true;
    }
}
