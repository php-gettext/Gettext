<?php
namespace Gettext\Translators;

abstract class BaseTranslator
{
    public static $current;

    /**
     * Set a translation instance as global, to use it with the gettext functions
     *
     * @param Translator $translator
     */
    public static function initGettextFunctions(Translator $translator)
    {
        self::$current = $translator;

        include_once __DIR__.'/functions.php';
    }

    /**
     * @see TranslatorInterface
     */
    public function register()
    {
        self::initGettextFunctions($this);
    }
}
