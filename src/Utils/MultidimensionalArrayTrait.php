<?php

namespace Gettext\Utils;

use Gettext\Translations;

/**
 * Trait used by all generators that exports the translations to multidimensional arrays (context => [original => [translation, plural1, pluraln...]]).
 */
trait MultidimensionalArrayTrait
{
    use HeadersGeneratorTrait;
    use HeadersExtractorTrait;

    /**
     * Returns a multidimensional array.
     * 
     * @param Translations $translations
     * @param bool         $includeHeaders
     * @param bool         $forceArray
     *
     * @return array
     */
    private static function toArray(Translations $translations, $includeHeaders, $forceArray = false)
    {
        $pluralForm = $translations->getPluralForms();
        $pluralLimit = is_array($pluralForm) ? ($pluralForm[0] - 1) : null;
        $messages = [];

        if ($includeHeaders) {
            $messages[''] = [
                '' => [self::generateHeaders($translations)],
            ];
        }

        foreach ($translations as $translation) {
            $context = $translation->getContext();
            $original = $translation->getOriginal();

            if (!isset($messages[$context])) {
                $messages[$context] = [];
            }

            if ($translation->hasPluralTranslations()) {
                $messages[$context][$original] = $translation->getPluralTranslations($pluralLimit);
                array_unshift($messages[$context][$original], $translation->getTranslation());
            } elseif ($forceArray) {
                $messages[$context][$original] = [$translation->getTranslation()];
            } else {
                $messages[$context][$original] = $translation->getTranslation();
            }
        }

        return $messages;
    }

    /**
     * Extract the entries from a multidimensional array.
     * 
     * @param array        $messages
     * @param Translations $translations
     */
    private static function fromArray(array $messages, Translations $translations)
    {
        foreach ($messages as $context => $contextTranslations) {
            foreach ($contextTranslations as $original => $value) {
                if ($context === '' && $original === '') {
                    self::extractHeaders(is_array($value) ? array_shift($value) : $value, $translations);
                    continue;
                }

                $translation = $translations->insert($context, $original);

                if (is_array($value)) {
                    $translation->setTranslation(array_shift($value));
                    $translation->setPluralTranslations($value);
                } else {
                    $translation->setTranslation($value);
                }
            }
        }
    }
}
