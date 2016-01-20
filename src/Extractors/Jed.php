<?php

namespace Gettext\Extractors;

use Gettext\Translations;

/**
 * Class to get gettext strings from json files.
 */
class Jed extends PhpArray implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations = null, $file = '')
    {
        if ($translations === null) {
            $translations = new Translations();
        }

        $content = json_decode($string, true);

        PhpArray::handleArray($content, $translations);

        return $translations;
    }

    /**
     * Extract and insert a new translation.
     * 
     * @param Translations $translations
     * @param string       $key
     * @param string       $message
     */
    protected static function insertTranslation(Translations $translations, $key, $message)
    {
        $context_glue = '\u0004';
        $key = explode($context_glue, $key);

        $context = isset($key[1]) ? array_shift($key) : '';
        $original = array_shift($key);
        $translation = array_shift($message);
        $plural_translation = array_shift($message);

        $entry = $translations->insert($context, $original);
        $entry->setTranslation($translation);
        $entry->setPluralTranslation($plural_translation);
    }
}
