<?php

namespace Gettext\Generators;

use Gettext\Translations;

class PhpArray extends Generator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function toString(Translations $translations)
    {
        $array = self::toArray($translations);

        return '<?php return '.var_export($array, true).'; ?>';
    }

    /**
     * Generates an array with the translations.
     *
     * @param Translations $translations
     *
     * @return array
     */
    public static function toArray(Translations $translations)
    {
        $array = static::buildArray($translations);

        $domain = $translations->getDomain() ?: 'messages';
        $lang = $translations->getLanguage() ?: 'en';

        $fullArray = array(
            $domain => array(
                '' => array(
                    'domain' => $domain,
                    'lang' => $lang,
                    'plural-forms' => 'nplurals=2; plural=(n != 1);',
                ),
            ),
        );

        if ($translations->getHeader('Plural-Forms') !== null) {
            $fullArray[$domain]['']['plural-forms'] = $translations->getHeader('Plural-Forms');
        }

        $fullArray[$domain] = array_merge($fullArray[$domain], $array);

        return $fullArray;
    }

    /**
     * Generates an array with all translations.
     * 
     * @param Translations $translations
     *
     * @return array
     */
    protected static function buildArray(Translations $translations)
    {
        $array = array();

        $context_glue = "\004";

        foreach ($translations as $translation) {
            $key = ($translation->hasContext() ? $translation->getContext().$context_glue : '').$translation->getOriginal();
            $entry = array($translation->getPlural(), $translation->getTranslation());

            if ($translation->hasPluralTranslation()) {
                $entry = array_merge($entry, $translation->getPluralTranslation());
            }

            $array[$key] = $entry;
        }

        return $array;
    }
}
