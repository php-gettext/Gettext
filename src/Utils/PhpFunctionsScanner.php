<?php

namespace Gettext\Utils;

class PhpFunctionsScanner extends FunctionsScanner
{
    protected $tokens;

    /**
     * Constructor.
     *
     * @param string $code The php code to scan
     */
    public function __construct($code)
    {
        $this->tokens = token_get_all($code);
    }

    /**
     * Decodes a T_CONSTANT_ENCAPSED_STRING string.
     *
     * @param string $value
     *
     * @return string
     */
    public static function decodeString($value)
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
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $count = count($this->tokens);
        $bufferFunctions = array();
        $functions = array();

        for ($k = 0; $k < $count; ++$k) {
            $value = $this->tokens[$k];

            //close the current function
            if (is_string($value)) {
                if ($value === ')' && isset($bufferFunctions[0])) {
                    $functions[] = array_shift($bufferFunctions);
                }

                continue;
            }

            //add an argument to the current function
            if (isset($bufferFunctions[0]) && ($value[0] === T_CONSTANT_ENCAPSED_STRING)) {
                $bufferFunctions[0][2][] = static::decodeString($value[1]);
                continue;
            }

            //new function found
            if (($value[0] === T_STRING) && is_string($this->tokens[$k + 1]) && ($this->tokens[$k + 1] === '(')) {
                array_unshift($bufferFunctions, array($value[1], $value[2], array()));
                ++$k;

                continue;
            }
        }

        return $functions;
    }
}
