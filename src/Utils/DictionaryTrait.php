<?php

namespace Gettext\Utils;

use Gettext\Translations;

/**
 * Trait used by all generators that exports the translations to plain dictionary (original => singular-translation).
 */
trait DictionaryTrait
{
    use HeadersGeneratorTrait;

    /**
     * Returns a plain dictionary with the format [original => translation].
     * 
     * @param Translations $translations
     * @param bool         $includeHeaders
     *
     * @return array
     */
    private static function toArray(Translations $translations, $includeHeaders)
    {
        $messages = [];

        if ($includeHeaders) {
            $messages[''] = self::generateHeaders($translations);
        }

        foreach ($translations as $translation) {
            $messages[$translation->getOriginal()] = $translation->getTranslation();
        }

        return $messages;
    }
}
