<?php
namespace Gettext;

class Translator
{
    private static $dictionary = array();
    private static $domain = 'messages';
    private static $pluralCount = 2;
    private static $pluralCode = 'return ($n != 1);';
    private static $context_glue = '\u0004';

    public static function loadTranslations($file)
    {
        if (is_file($file)) {
            $dictionary = include($file);
            self::loadTranslationsArray($dictionary);
        }
    }

    public static function loadTranslationsArray($dictionary)
    {
        if (is_array($dictionary)) {
            $domain = isset($dictionary['messages']['']['domain']) ? $dictionary['messages']['']['domain'] : null;

            // If a plural form is set we extract those values
            if (isset($dictionary['messages']['']['plural-forms'])) {
                list($count, $code) = explode(';', $dictionary['messages']['']['plural-forms']);
                self::$pluralCount = (int) str_replace('nplurals=','', $count);

                // extract just the expression turn 'n' into a php variable '$n'.
                // Slap on a return keyword and semicolon at the end.
                self::$pluralCode = str_replace('plural=', 'return ', str_replace('n', '$n', $code)) . ';';
            }

            unset($dictionary['messages']['']);
            self::addTranslations($dictionary['messages'], $domain);
        }
    }

    public static function addTranslations(array $dictionary, $domain = null)
    {
        if ($domain === null) {
            $domain = self::$domain;
        }

        if (!isset(self::$dictionary[$domain])) {
            self::$dictionary[$domain] = array();
        }

        self::$dictionary[$domain] = array_replace_recursive(self::$dictionary[$domain], $dictionary);
    }

    public static function clearTranslations()
    {
        self::$dictionary = array();
    }

    public static function getTranslation($domain, $context, $original)
    {
        $key = isset($context) ? $context.self::$context_glue.$original : $original;

        return isset(self::$dictionary[$domain][$key]) ? self::$dictionary[$domain][$key] : false;
    }

    public static function gettext($original)
    {
        return self::dpgettext(self::$domain, null, $original);
    }

    public static function ngettext($original, $plural, $value)
    {
        return self::dnpgettext(self::$domain, null, $original, $plural, $value);
    }

    public static function dngettext($domain, $original, $plural, $value)
    {
        return self::dnpgettext($domain, null, $original, $plural, $value);
    }

    public static function npgettext($context, $original, $plural, $value)
    {
        return self::dnpgettext(self::$domain, $context, $original, $plural, $value);
    }

    public static function pgettext($context, $original)
    {
        return self::dpgettext(self::$domain, $context, $original);
    }

    public static function dgettext($domain, $original)
    {
        return self::dpgettext($domain, null, $original);
    }

    public static function dpgettext($domain, $context, $original)
    {
        $translation = self::getTranslation($domain, $context, $original);

        if (isset($translation[1]) && $translation[1] !== '') {
            return $translation[1];
        }

        return $original;
    }

    public static function dnpgettext($domain, $context, $original, $plural, $value)
    {
        $key = self::isPlural($value);
        $translation = self::getTranslation($domain, $context, $original);

        if (isset($translation[$key]) && $translation[$key] !== '') {
            return $translation[$key];
        }

        return ($key === 1) ? $original : $plural;
    }

    /**
     * Executes the plural decision code given the number to decide which
     * plural version to take.
     *
     * @param $n
     * @return int
     */
    public static function isPlural($n)
    {
        $pluralFunc = create_function('$n', self::fixTerseIfs(self::$pluralCode));

        if (self::$pluralCount <= 2) {
            return ($pluralFunc($n)) ? 2 : 1;
        } else {
            // We need to +1 because while (GNU) gettext codes assume 0 based,
            // this gettext actually stores 1 based.
            return ($pluralFunc($n)) + 1;
        }
    }

    /**
     * This function will recursively wrap failure states in brackets if they contain a nested terse if
     *
     * This because PHP can not handle nested terse if's unless they are wrapped in brackets.
     *
     * This code probably only works for the gettext plural decision codes.
     *
     * return ($n==1 ? 0 : $n%10>=2 && $n%10<=4 && ($n%100<10 || $n%100>=20) ? 1 : 2);
     * becomes
     * return ($n==1 ? 0 : ($n%10>=2 && $n%10<=4 && ($n%100<10 || $n%100>=20) ? 1 : 2));
     *
     * @param  string $code  the terse if string
     * @param  bool   $inner If inner is true we wrap it in brackets
     * @return string A formatted terse If that PHP can work with.
     */
    public static function fixTerseIfs($code, $inner=false)
    {
        /**
         * (?P<expression>[^?]+)   Capture everything up to ? as 'expression'
         * \?                      ?
         * (?P<success>[^:]+)      Capture everything up to : as 'success'
         * :                       :
         * (?P<failure>[^;]+)      Capture everything up to ; as 'failure'
         */
        preg_match('/(?P<expression>[^?]+)\?(?P<success>[^:]+):(?P<failure>[^;]+)/', $code, $matches);

        // If no match was found then no terse if was present
        if (!isset($matches[0]))
            return $code;

        $expression = $matches['expression'];
        $success    = $matches['success'];
        $failure    = $matches['failure'];

        // Go look for another terse if in the failure state.
        $failure = self::fixTerseIfs($failure, true);
        $code = $expression . ' ? ' . $success . ' : ' . $failure;

        if ($inner) {
            return "($code)";
        } else {
            // note the semicolon. We need that for executing the code.
            return "$code;";
        }
    }
}
