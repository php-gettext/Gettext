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
        $concatenating = false;

        for ($k = 0; $k < $count; ++$k) {
            $value = $this->tokens[$k];

            if (is_string($value)) {
                if ($value === '.') {
                    //concatenating strings
                    $concatenating = true;
                    continue;
                }
                $concatenating = false;
                if ($value === ')' && isset($bufferFunctions[0])) {
                    //close the current function
                    $functions[] = array_shift($bufferFunctions);
                }
                continue;
            }

            switch ($value[0]) {
                case T_CONSTANT_ENCAPSED_STRING:
                    //add an argument to the current function
                    if (isset($bufferFunctions[0])) {
                        $string = \Gettext\Extractors\PhpCode::convertString($value[1]);
                        if ($concatenating && !empty($bufferFunctions[0][2])) {
                            $bufferFunctions[0][2][count($bufferFunctions[0][2]) - 1] .= $string;
                        } else {
                            $bufferFunctions[0][2][] = $string;
                        }
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
            if ($concatenating && $value[0] !== T_COMMENT && $value[0] !== T_WHITESPACE) {
                $concatenating = false;
            }
        }

        return $functions;
    }
}
