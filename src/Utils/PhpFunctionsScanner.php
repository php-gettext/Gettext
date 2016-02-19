<?php

namespace Gettext\Utils;

use Gettext\Extractors\PhpCode;

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
        /* @var ParsedFunction[] $bufferFunctions */
        $functions = array();
        /* @var ParsedFunction[] $functions */

        for ($k = 0; $k < $count; ++$k) {
            $value = $this->tokens[$k];

            if (is_string($value)) {
                $s = $value;
            } else {
                $s = token_name($value[0]).' >'.$value[1].'<';
            }

            if (is_string($value)) {
                if (isset($bufferFunctions[0])) {
                    switch ($value) {
                        case ',':
                            $bufferFunctions[0]->nextArgument();
                            break;
                        case ')':
                            $functions[] = array_shift($bufferFunctions)->close();
                            break;
                        case '.':
                            break;
                        default:
                            $bufferFunctions[0]->stopArgument();
                            break;
                    }
                }
                continue;
            }

            switch ($value[0]) {
                case T_CONSTANT_ENCAPSED_STRING:
                    //add an argument to the current function
                    if (isset($bufferFunctions[0])) {
                        $bufferFunctions[0]->addArgumentChunk(PhpCode::convertString($value[1]));
                    }
                    break;
                case T_STRING:
                    if (isset($bufferFunctions[0])) {
                        $bufferFunctions[0]->stopArgument();
                    }
                    //new function found
                    for ($j = $k + 1; $j < $count; ++$j) {
                        $nextToken = $this->tokens[$j];
                        if (is_array($nextToken) && ($nextToken[0] === T_COMMENT || $nextToken[0] === T_WHITESPACE)) {
                            continue;
                        }
                        if ($nextToken === '(') {
                            array_unshift($bufferFunctions, new ParsedFunction($value[1], $value[2]));
                            $k = $j;
                        }
                        break;
                    }
                    break;
                case T_WHITESPACE:
                case T_COMMENT:
                    break;
                default:
                    if (isset($bufferFunctions[0])) {
                        $bufferFunctions[0]->stopArgument();
                    }
                    break;
            }
        }

        return $functions;
    }
}
