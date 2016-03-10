<?php

namespace Gettext;

abstract class BaseTranslator implements TranslatorInterface
{
    /** @var TranslatorInterface */
    public static $current;

    /**
     * Set a translation instance as global, to use it with the gettext functions.
     *
     * @param TranslatorInterface $translator
     */
    public static function initGettextFunctions(TranslatorInterface $translator)
    {
        self::$current = $translator;

        include_once __DIR__.'/translator_functions.php';
    }

    /**
     * @see TranslatorInterface
     */
    public function register()
    {
        self::initGettextFunctions($this);
    }
}
