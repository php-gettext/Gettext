<?php
namespace Gettext\Utils;

class JsFunctionsScanner extends FunctionsScanner
{
    protected $code;
    protected $status = array();

    /**
     * Constructor
     *
     * @param string $code The php code to scan
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        $length = strlen($this->code);
        $line = 1;
        $buffer = '';
        $functions = array();
        $bufferFunctions = array();
        $char = null;

        for ($pos = 0; $pos < $length; $pos++) {
            $prev = $char;
            $char = $this->code[$pos];
            $next = isset($this->code[$pos]) ? $this->code[$pos] : null;

            switch ($char) {
                case "\n":
                    ++$line;

                    if ($this->status('line-comment')) {
                        $this->upStatus();
                    }
                    break;

                case "/":
                    switch ($this->status()) {
                        case 'simple-quote':
                        case 'double-quote':
                        case 'line-comment':
                            break;

                        case 'block-comment':
                            if ($prev === '*') {
                                $this->upStatus();
                            }
                            break;

                        default:
                            if ($next === '/') {
                                $this->downStatus('line-comment');
                            } elseif ($next === '*') {
                                $this->downStatus('block-comment');
                            }
                            break;
                    }
                    break;

                case "'":
                    switch ($this->status()) {
                        case 'simple-quote':
                            $this->upStatus();
                            break;

                        case 'line-comment':
                        case 'block-comment':
                            break;

                        default:
                            $this->downStatus('simple-quote');
                            break;
                    }
                    break;

                case '"':
                    switch ($this->status()) {
                        case 'double-quote':
                            $this->upStatus();
                            break;

                        case 'line-comment':
                        case 'block-comment':
                            break;

                        default:
                            $this->downStatus('double-quote');
                            break;
                    }
                    break;

                case '(':
                    switch ($this->status()) {
                        case 'double-quote':
                        case 'line-comment':
                        case 'block-comment':
                            break;

                        default:
                            if ($buffer && preg_match('/(\w+)$/', $buffer, $matches)) {
                                $this->downStatus('function');
                                array_unshift($bufferFunctions, array($matches[1], $line, array()));
                                $buffer = '';
                                continue 3;
                            }
                            break;
                    }
                    break;

                case ')':
                    switch ($this->status()) {
                        case 'function':
                            if ($buffer && ($buffer[0] === '"' || $buffer[0] === "'")) {
                                if ($buffer[0] === '"') {
                                    $buffer = str_replace('\\"', '"', $buffer);
                                } else {
                                    $buffer = str_replace("\\'", "'", $buffer);
                                }

                                $bufferFunctions[0][2][] = substr($buffer, 1, -1);
                            }

                            if ($bufferFunctions) {
                                $functions[] = array_shift($bufferFunctions);
                            }

                            $buffer = '';
                            continue 3;
                    }

                case ',':
                    switch ($this->status()) {
                        case 'function':
                            if ($buffer && ($buffer[0] === '"' || $buffer[0] === "'")) {
                                if ($buffer[0] === '"') {
                                    $buffer = str_replace('\\"', '"', $buffer);
                                } else {
                                    $buffer = str_replace("\\'", "'", $buffer);
                                }

                                $bufferFunctions[0][2][] = substr($buffer, 1, -1);
                            }

                            $buffer = '';
                            continue 3;
                    }
            }

            switch ($this->status()) {
                case 'line-comment':
                case 'block-comment':
                    break;

                default:
                    $buffer .= $char;
                    break;
            }
        }

        return $functions;
    }

    protected function status($match = null)
    {
        $status = isset($this->status[0]) ? $this->status[0] : null;

        if ($match) {
            return ($status === $match);
        }

        return $status;
    }

    protected function downStatus($status)
    {
        array_unshift($this->status, $status);
    }

    protected function upStatus()
    {
        return array_shift($this->status);
    }
}
