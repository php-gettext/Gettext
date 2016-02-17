<?php

namespace Gettext\Utils;

class Strings
{
    /**
     * Decodes a T_CONSTANT_ENCAPSED_STRING string.
     *
     * @param string $value
     *
     * @return string
     */
    public static function fromPhp($value)
    {
        if ($value[0] === "'" || strpos($value, '$') === false) {
            if (strpos($value, '\\') === false) {
                return substr($value, 1, -1);
            }

            return eval("return $value;");
        }

        $result = '';
        $value = substr($value, 1, -1);

        while (($p = strpos($value, '\\')) !== false) {
            if (!isset($value[$p + 1])) {
                break;
            }

            if ($p > 0) {
                $result .= substr($value, 0, $p);
            }

            $value = substr($value, $p + 1);
            $p = strpos($value, '$');

            if ($p === false) {
                $result .= eval('return "\\'.$value.'";');
                $value = '';
                break;
            }

            if ($p === 0) {
                $result .= '$';
                $value = substr($value, 1);
            } else {
                $result .= eval('return "\\'.substr($value, 0, $p).'";');
                $value = substr($value, $p);
            }
        }

        return $result.$value;
    }

    /**
     * Convert a string to its PO representation.
     *
     * @param string $value
     *
     * @return string
     */
    public static function toPo($value)
    {
        return '"'.strtr(
            $value,
            array(
                "\x00" => '',
                '\\' => '\\\\',
                "\t" => '\t',
                "\n" => '\n',
            )
        ).'"';
    }

    /**
     * Convert a string from its PO representation.
     *
     * @param string $value
     *
     * @return string
     */
    public static function fromPo($value)
    {
        if (!$value) {
            return '';
        }

        if ($value[0] === '"') {
            $value = substr($value, 1, -1);
        }

        return strtr(
            $value,
            array(
                '\\\\' => '\\',
                '\\a' => "\x07",
                '\\b' => "\x08",
                '\\t' => "\t",
                '\\n' => "\n",
                '\\v' => "\x0b",
                '\\f' => "\x0c",
                '\\r' => "\r",
                '\\"' => '"',
            )
        );
    }
}
