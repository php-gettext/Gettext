<?php

namespace Gettext\Extractors;

use Gettext\Translations;
use Gettext\Utils\JsFunctionsScanner;

/**
 * Class to get gettext strings from javascript files.
 */
class JsCode extends Extractor implements ExtractorInterface
{
    public static $functions = array(
        'gettext' => 'gettext',
        '__' => 'gettext',
        'ngettext' => 'ngettext',
        'n__' => 'ngettext',
        'pgettext' => 'pgettext',
        'p__' => 'pgettext',
        'dgettext' => 'dgettext',
        'd__' => 'dgettext',
        'dpgettext' => 'dpgettext',
        'dp__' => 'dpgettext',
        'npgettext' => 'npgettext',
        'np__' => 'npgettext',
        'dnpgettext' => 'dnpgettext',
        'dnp__' => 'dnpgettext',
    );

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations = null, $file = '')
    {
        if ($translations === null) {
            $translations = new Translations();
        }

        $functions = new JsFunctionsScanner($string);
        $functions->saveGettextFunctions(self::$functions, $translations, $file);
    }
}
