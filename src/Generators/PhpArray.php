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
        return [
            'domain' => $translations->getDomain() ?: 'messages',
            'lang' => $translations->getLanguage() ?: 'en',
            'plural-forms' => $translations->getHeader('Plural-Forms') ?: 'nplurals=2; plural=(n != 1);',
            'messages' => static::buildArray($translations)
        ];
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
        $messages = [];

        foreach ($translations as $translation) {
            $context = (string) $translation->getContext();

            if (!isset($messages[$context])) {
                $messages[$context] = [];
            }

            $message = $translation->getPluralTranslations();
            array_unshift($message, $translation->getTranslation());

            $messages[$context][$translation->getOriginal()] = $message;
        }

        return $messages;
    }
}
