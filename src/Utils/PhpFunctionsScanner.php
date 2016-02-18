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

            switch ($value[0]) {
                case T_CONSTANT_ENCAPSED_STRING:
                    //add an argument to the current function
                    if (isset($bufferFunctions[0])) {
                        $bufferFunctions[0][2][] = \Gettext\Extractors\PhpCode::convertString($value[1]);
                    }
                    break;
                case T_STRING:
                    //new function found
                    for ($j = $k + 1; $j < $count; $j++) {
                        $nextToken = $this->tokens[$j];
                        if (is_array($nextToken) && ($nextToken[0] === T_COMMENT || $nextToken[0] === T_WHITESPACE)) {
                            continue;
                        }
                        if ($nextToken === '(') {
                            array_unshift($bufferFunctions, array($value[1], $value[2], array()));
                            $k = $j;
                        }
                        break;
                    }
                    break;
            }
        }

        return $functions;
    }
}
