<?php
use Gettext\Translator;

/**
 * Returns the translation of a string
 *
 * @param string $original
 *
 * @return string
 */
function __($original)
{
    $text = Translator::$current->gettext($original);

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
    $text = Translator::$current->ngettext($original, $plural, $value);

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
    $text = Translator::$current->pgettext($context, $original);

    if (func_num_args() === 2) {
        return $text;
    }

    $args = array_slice(func_get_args(), 2);

    return vsprintf($text, is_array($args[0]) ? $args[0] : $args);
}

/**
 * Returns the translation of a string in a specific domain
 *
 * @param string $domain
 * @param string $original
 *
 * @return string
 */
function d__($domain, $original)
{
    $text = Translator::dgettext($domain, $original);

    if (func_num_args() === 2) {
        return $text;
    }

    $args = array_slice(func_get_args(), 2);

    return vsprintf($text, is_array($args[0]) ? $args[0] : $args);
}

/**
 * Returns the translation of a string in a specific domain and context
 *
 * @param string $domain
 * @param string $context
 * @param string $original
 *
 * @return string
 */
function dp__($domain, $context, $original)
{
    $text = Translator::dpgettext($domain, $context, $original);

    if (func_num_args() === 3) {
        return $text;
    }

    $args = array_slice(func_get_args(), 3);

    return vsprintf($text, is_array($args[0]) ? $args[0] : $args);
}

/**
 * Returns the singular/plural translation of a string in a specific domain and context
 *
 * @param string $domain
 * @param string $context
 * @param string $original
 * @param string $plural
 * @param string $value
 *
 * @return string
 */
function dnp__($domain, $context, $original, $plural, $value)
{
    $text = Translator::dnpgettext($domain, $context, $original, $plural, $value);

    if (func_num_args() === 5) {
        return $text;
    }

    $args = array_slice(func_get_args(), 5);

    return vsprintf($text, is_array($args[0]) ? $args[0] : $args);
}

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

/**
 * Prints function result
 *
 * @see d__
 */
function d__e()
{
    echo call_user_func_array('d__', func_get_args());
}

/**
 * Prints function result
 *
 * @see dp__
 */
function dp__e()
{
    echo call_user_func_array('dp__', func_get_args());
}

/**
 * Prints function result
 *
 * @see dnp__
 */
function dnp__e()
{
    echo call_user_func_array('dnp__', func_get_args());
}
