<?php

namespace Gettext\Generators;

use Gettext\Translations;
use Gettext\Utils\MultidimensionalArrayTrait;

class PhpArray extends Generator implements GeneratorInterface
{
    use MultidimensionalArrayTrait {
        MultidimensionalArrayTrait::toArray as toMultidimensionalArray;
    }

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
            'messages' => self::toMultidimensionalArray($translations, $options['includeHeaders'], true),
        ];
    }
}
