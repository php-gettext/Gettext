<?php

namespace Gettext\Extractors;

use Gettext\Translations;

interface ExtractorInterface
{
    /**
     * Extract the translations from a file.
     *
     * @param array|string      $file         A path of a file or files
     * @param null|Translations $translations The translations instance to append the new translations.
     *
     * @return Translations
     */
    public static function fromFile($file, Translations $translations = null);

    /**
     * Parses a string and append the translations found in the Translations instance.
     *
     * @param string            $string
     * @param Translations|null $translations
     * @param string            $file         The file path to insert the reference
     *
     * @return Translations
     */
    public static function fromString($string, Translations $translations = null, $file = '');
}
