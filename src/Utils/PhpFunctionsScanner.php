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
                $bufferFunctions[0][2][] = Strings::fromPhp($value[1]);
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
