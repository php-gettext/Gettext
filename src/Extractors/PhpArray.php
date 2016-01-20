<?php

namespace Gettext\Extractors;

use Exception;
use Gettext\Translations;

/**
 * Class to get gettext strings from php files returning arrays.
 */
class PhpArray extends Extractor implements ExtractorInterface
{
    /**
     * Extract the translations from a file.
     *
     * @param array|string      $file         A path of a file or files
     * @param null|Translations $translations The translations instance to append the new translations.
     *
     * @return Translations
     */
    public static function fromFile($file, Translations $translations = null)
    {
        if ($translations === null) {
            $translations = new Translations();
        }

        foreach (self::getFiles($file) as $file) {
            self::handleArray(include($file), $translations);
        }

        return $translations;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations = null, $file = '')
    {
        throw new Exception('PhpArray::fromString() cannot be called. Use PhpArray::fromFile()');
    }

    /**
     * Handle an array of translations and append to the Translations instance.
     *
     * @param array        $content
     * @param Translations $translations
     */
    public static function handleArray(array $content, Translations $translations)
    {
        $content = current($content);

        $translations_info = isset($content['']) ? $content[''] : null;
        unset($content['']);

        if (isset($translations_info['domain'])) {
            $translations->setDomain($translations_info['domain']);
        }

        foreach ($content as $key => $message) {
            static::insertTranslation($translations, $key, $message);
        }
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
        $plural = array_shift($message);
        $translation = array_shift($message);
        $plural_translation = array_shift($message);

        $entry = $translations->insert($context, $original, $plural);
        $entry->setTranslation($translation);
        $entry->setPluralTranslation($plural_translation);
    }
}
