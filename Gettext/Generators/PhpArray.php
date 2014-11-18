<?php
namespace Gettext\Generators;

use Gettext\Entries;

class PhpArray extends Generator
{
    /**
     * {@inheritDoc}
     */
    public static function toString(Entries $entries)
    {
        $array = self::toArray($entries);

        return '<?php return '.var_export($array, true).'; ?>';
    }


    /**
     * Generates an array with the entries
     * 
     * @param Entries $entries
     * 
     * @return array
     */
    public static function toArray(Entries $entries)
    {
        $array = array();

        $context_glue = "\004";

        foreach ($entries as $translation) {
            $key = ($translation->hasContext() ? $translation->getContext().$context_glue : '').$translation->getOriginal();
            $entry = array($translation->getPlural(), $translation->getTranslation());

            if ($translation->hasPluralTranslation()) {
                $entry = array_merge($entry, $translation->getPluralTranslation());
            }

            $array[$key] = $entry;
        }

        $domain = $entries->getDomain() ?: 'messages';
        $lang = $entries->getLanguage() ?: 'en';

        $translations = array(
            $domain => array(
                '' => array(
                    'domain' => $domain,
                    'lang' => $lang,
                    'plural-forms' => 'nplurals=2; plural=(n != 1);'
                )
            )
        );

        if ($entries->getHeader('Plural-Forms') !== null) {
            $translations[$domain]['']['plural-forms'] = $entries->getHeader('Plural-Forms');
        }

        $translations[$domain] = array_merge($translations[$domain], $array);

        return $translations;
    }
}
