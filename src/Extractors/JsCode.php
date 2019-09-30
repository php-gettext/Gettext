<?php

namespace Gettext\Extractors;

use Exception;
use Gettext\Translations;
use Gettext\Utils\JsFunctionsScanner;

/**
 * Class to get gettext strings from javascript files.
 */
class JsCode extends Extractor implements ExtractorInterface, ExtractorMultiInterface
{
    public static $options = [
        'constants' => [],

        'functions' => [
            'gettext' => 'gettext',
            '__' => 'gettext',
            'ngettext' => 'ngettext',
            'n__' => 'ngettext',
            'pgettext' => 'pgettext',
            'p__' => 'pgettext',
            'dgettext' => 'dgettext',
            'd__' => 'dgettext',
            'dngettext' => 'dngettext',
            'dn__' => 'dngettext',
            'dpgettext' => 'dpgettext',
            'dp__' => 'dpgettext',
            'npgettext' => 'npgettext',
            'np__' => 'npgettext',
            'dnpgettext' => 'dnpgettext',
            'dnp__' => 'dnpgettext',
            'noop' => 'noop',
            'noop__' => 'noop',
        ],
    ];

    /**
     * @inheritdoc
     * @throws Exception
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        self::fromStringMultiple($string, [$translations], $options);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public static function fromStringMultiple($string, array $translations, array $options = [])
    {
        $options += static::$options;

        $functions = new JsFunctionsScanner($string);
        $functions->saveGettextFunctions($translations, $options);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public static function fromFileMultiple($file, array $translations, array $options = [])
    {
        foreach (self::getFiles($file) as $file) {
            $options['file'] = $file;
            static::fromStringMultiple(self::readFile($file), $translations, $options);
        }
    }
}
