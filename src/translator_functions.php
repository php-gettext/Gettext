<?php
use Gettext\Translator;

/**
 * Get/set a new current translator
 *
 * @param Translator $translator
 *
 * @return null|Translator
 */
function __currentTranslator(Translator $translator = null)
{
    static $currentTranslator;

    if ($translator === null) {
        if (!$currentTranslator) {
            throw new Exception("You must specify a translator instance before use the Gettext functions");
        }

        return $currentTranslator;
    }

    $currentTranslator = $translator;
}

/**
 * Returns the translation of a string
 *
 * @param string $original
 *
 * @return string
 */
function __($original)
{
    $text = __currentTranslator()->gettext($original);

    if (func_num_args() === 1) {
        return $text;
    }

    $args = array_slice(func_get_args(), 1);

    return vsprintf($text, is_array($args[0]) ? $args[0] : $args);
}

/**
 * Returns the singular/plural translation of a string
 *
 * @param string $original
 * @param string $plural
 * @param string $value
 *
 * @return string
 */
function n__($original, $plural, $value)
{
    $text = __currentTranslator()->ngettext($original, $plural, $value);

    if (func_num_args() === 3) {
        return $text;
    }

    $args = array_slice(func_get_args(), 3);

    return vsprintf($text, is_array($args[0]) ? $args[0] : $args);
}

/**
 * Returns the translation of a string in a specific context
 *
 * @param string $context
 * @param string $original
 *
 * @return string
 */
function p__($context, $original)
{
    $text = __currentTranslator()->pgettext($context, $original);

    if (func_num_args() === 2) {
        return $text;
    }

    $args = array_slice(func_get_args(), 2);

    return vsprintf($text, is_array($args[0]) ? $args[0] : $args);
}
/*

Not implemented yet...

function __d($domain, $original)
{
    $text = Translator::dgettext($domain, $original);

    if (func_num_args() === 2) {
        return $text;
    }

    $args = array_slice(func_get_args(), 2);

    return vsprintf($text, is_array($args[0]) ? $args[0] : $args);
}

function __dp($domain, $context, $original)
{
    $text = Translator::dpgettext($domain, $context, $original);

    if (func_num_args() === 3) {
        return $text;
    }

    $args = array_slice(func_get_args(), 3);

    return vsprintf($text, is_array($args[0]) ? $args[0] : $args);
}

function __dnp($domain, $context, $original, $plural, $value)
{
    $text = Translator::dnpgettext($domain, $context, $original, $plural, $value);

    if (func_num_args() === 5) {
        return $text;
    }

    $args = array_slice(func_get_args(), 5);

    return vsprintf($text, is_array($args[0]) ? $args[0] : $args);
}
*/

/**
 * Prints function result
 *
 * @see __
 */
function __e()
{
    echo call_user_func_array('__', func_get_args());
}

/**
 * Prints function result
 *
 * @see n__
 */
function n__e()
{
    echo call_user_func_array('n__', func_get_args());
}

/**
 * Prints function result
 *
 * @see p__
 */
function p__e()
{
    echo call_user_func_array('p__', func_get_args());
}
