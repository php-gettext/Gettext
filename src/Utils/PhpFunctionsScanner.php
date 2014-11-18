<?php
namespace Gettext\Utils;

class PhpFunctionsScanner
{
    protected $tokens;
    protected $functions = array();


    /**
     * Constructor
     * 
     * @param string $code The php code to scan
     */
    public function __construct($code)
    {
        $this->tokens = token_get_all($code);
        $this->scan();
    }

    /**
     * Scan and save the functions and the arguments
     * 
     * @return void
     */
    protected function scan()
    {
        $count = count($this->tokens);
        $bufferFunctions = array();

        for ($k = 0; $k < $count; $k++) {
            $value = $this->tokens[$k];

            //close the current function
            if (is_string($value)) {
                if ($value === ')' && isset($bufferFunctions[0])) {
                    $this->functions[] = array_shift($bufferFunctions);
                }

                continue;
            }

            //add an argument to the current function
            if (isset($bufferFunctions[0]) && ($value[0] === T_CONSTANT_ENCAPSED_STRING)) {
                $val = $value[1];

                if ($val[0] === '"') {
                    $val = str_replace('\\"', '"', $val);
                } else {
                    $val = str_replace("\\'", "'", $val);
                }

                $bufferFunctions[0][2][] = substr($val, 1, -1);
                continue;
            }

            //new function found
            if (($value[0] === T_STRING) && is_string($this->tokens[$k + 1]) && ($this->tokens[$k + 1] === '(')) {
                array_unshift($bufferFunctions, array($value[1], $value[2], array()));
                $k++;

                continue;
            }
        }
    }

    /**
     * Returns the functions found
     * 
     * @return array
     */
    public function getFunctions()
    {
        return $this->functions;
    }
}
