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

        foreach (static::getFiles($file) as $file) {
            static::extract(include($file), $translations);
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
    public static function extract(array $content, Translations $translations)
    {
        if (!empty($content['domain'])) {
            $translations->setDomain($content['domain']);
        }

        if (!empty($content['lang'])) {
            $translations->setLanguage($content['lang']);
        }

        if (!empty($content['plural-forms'])) {
            $translations->setHeader(Translations::HEADER_PLURAL, $content['plural-forms']);
        }

        foreach ($content['messages'] as $context => $messages) {
            foreach ($messages as $original => $translation) {
                $translations->insert($context, $original)
                    ->setTranslation(array_shift($translation))
                    ->setPluralTranslations($translation);
            }
        }
    }
}
