<?php

namespace Gettext\Generators;

use Gettext\Translations;

class PhpArray extends Generator implements GeneratorInterface
{
    public static $options = [
        'includeHeaders' => true,
    ];

    /**
     * {@inheritdoc}
     */
    public static function toString(Translations $translations, array $options = [])
    {
        $array = self::toArray($translations, $options);

        return '<?php return '.var_export($array, true).';';
    }

    /**
     * Generates an array with the translations.
     *
     * @param Translations $translations
     * @param array        $options
     *
     * @return array
     */
    public static function toArray(Translations $translations, array $options = [])
    {
        $options += static::$options;

        return [
            'domain' => $translations->getDomain(),
            'plural-forms' => $translations->getHeader('Plural-Forms'),
            'messages' => self::buildMessages($translations, $options),
        ];
    }

    /**
     * Generates an array with all translations.
     * 
     * @param Translations $translations
     * @param array        $options
     *
     * @return array
     */
    private static function buildMessages(Translations $translations, array $options)
    {
        $pluralForm = $translations->getPluralForms();
        $pluralLimit = is_array($pluralForm) ? ($pluralForm[0] - 1) : null;
        $messages = [];

        if ($options['includeHeaders']) {
            $headers = '';

            foreach ($translations->getHeaders() as $name => $value) {
                $headers .= "{$name}: {$value}\n";
            }

            if ($headers !== '') {
                $messages[''] = ['' => [$headers]];
            }
        }

        foreach ($translations as $translation) {
            $context = (string) $translation->getContext();

            if (!isset($messages[$context])) {
                $messages[$context] = [];
            }

            if ($translation->hasPluralTranslations()) {
                $message = $translation->getPluralTranslations($pluralLimit);
                array_unshift($message, $translation->getTranslation());
            } else {
                $message = [$translation->getTranslation()];
            }

            $messages[$context][$translation->getOriginal()] = $message;
        }

        return $messages;
    }
}
