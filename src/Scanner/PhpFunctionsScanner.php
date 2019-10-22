<?php
declare(strict_types = 1);

namespace Gettext\Scanner;

class PhpFunctionsScanner implements FunctionsScannerInterface
{
    protected $comments = true;
    protected $constants = [];

    /**
     * Include related comments to the functions
     * 
     * @param bool|array $comments Boolean to enable/disable. Array to filter comments by prefixes
     */
    public function includeComments($comments = true): void
    {
        $this->comments = $comments;
    }

    /**
     * Replace constants found in the code by values
     */
    public function setDefinedConstants(array $constants): void
    {
        $this->constants = $constants;
    }

    public function scan(string $code, string $filename = null): array
    {
        $tokens = static::tokenize($code);
        $total = count($tokens);

        $bufferFunctions = [];
        $lastComment = null;
        $functions = [];

        $token = current($tokens);
        $lastLine = 1;

        while ($token !== false) {
            $nextToken = next($tokens);

            if (is_string($token)) {
                if (isset($bufferFunctions[0])) {
                    switch ($token) {
                        case ',':
                            if (!$bufferFunctions[0]->countArguments()) {
                                $bufferFunctions[0]->addArgument();
                            }
                            $bufferFunctions[0]->addArgument();
                            break;

                        case ')':
                            $functions[] = array_shift($bufferFunctions)->setLastLine($lastLine);
                            break;

                        case '.':
                            break;

                        default:
                            if (!$bufferFunctions[0]->countArguments()) {
                                $bufferFunctions[0]->addArgument();
                            }
                            $bufferFunctions[0]->closeArgument();
                            break;
                    }
                }

                $token = $nextToken;
                continue;
            }

            $lastLine = $token[2];

            switch ($token[0]) {
                case T_CONSTANT_ENCAPSED_STRING:
                    if (isset($bufferFunctions[0])) {
                        $bufferFunctions[0]->addArgumentChunk(static::decode($token[1]));
                    }
                    break;

                case T_LNUMBER:
                case T_DNUMBER:
                    if (isset($bufferFunctions[0])) {
                        $bufferFunctions[0]->addArgumentChunk((string) $token[1]);
                    }
                    break;

                case T_STRING:
                    //New function found
                    if ($nextToken === '(') {
                        $function = new ParsedFunction($token[1], $filename, $token[2]);

                        if ($lastComment) {
                            if ($lastComment->isRelatedWith($function)) {
                                $function->addComment($lastComment);
                            }

                            $lastComment = null;
                        }

                        array_unshift($bufferFunctions, $function);
                        $nextToken = next($tokens);
                        break;
                    }

                    if (isset($this->constants[$token[1]])) {
                        $bufferFunctions[0]->addArgumentChunk($this->constants[$token[1]]);
                        break;
                    }

                    if (isset($bufferFunctions) && !$bufferFunctions[0]->countArguments()) {
                        $bufferFunctions[0]->addArgument();
                    }
                    break;

                case T_COMMENT:
                    if ($this->comments === false) {
                        break;
                    }

                    $comment = new ParsedComment(trim($token[1]), $filename, $token[2]);

                    if ($this->comments !== true && !$comment->isPrefixed($this->comments)) {
                        break;
                    }

                    // The comment is inside the function call.
                    if (isset($bufferFunctions[0])) {
                        $bufferFunctions[0]->addComment($comment);
                    } else {
                        $lastComment = $comment;
                    }

                    break;

                default:
                    if (isset($bufferFunctions[0])) {
                        if (!$bufferFunctions[0]->countArguments()) {
                            $bufferFunctions[0]->addArgument();
                        }

                        $bufferFunctions[0]->closeArgument();
                    }
                    break;
            }

            $token = $nextToken;
        }

        return $functions;
    }

    protected static function tokenize(string $code): array
    {
        return array_values(
            array_filter(
                token_get_all($code),
                function ($token) {
                    return !is_array($token) || $token[0] !== T_WHITESPACE;
                }
            )
        );
    }

    /**
     * Decodes a T_CONSTANT_ENCAPSED_STRING string.
     */
    protected static function decode(string $value): string
    {
        if (strpos($value, '\\') === false) {
            return substr($value, 1, -1);
        }

        if ($value[0] === "'") {
            return strtr(substr($value, 1, -1), ['\\\\' => '\\', '\\\'' => '\'']);
        }

        $value = substr($value, 1, -1);

        return preg_replace_callback(
            '/\\\(n|r|t|v|e|f|\$|"|\\\|x[0-9A-Fa-f]{1,2}|u{[0-9a-f]{1,6}}|[0-7]{1,3})/',
            function ($match) {
                switch ($match[1][0]) {
                    case 'n':
                        return "\n";
                    case 'r':
                        return "\r";
                    case 't':
                        return "\t";
                    case 'v':
                        return "\v";
                    case 'e':
                        return "\e";
                    case 'f':
                        return "\f";
                    case '$':
                        return '$';
                    case '"':
                        return '"';
                    case '\\':
                        return '\\';
                    case 'x':
                        return chr(hexdec(substr($match[1], 1)));
                    case 'u':
                        return self::unicodeChar(hexdec(substr($match[1], 1)));
                    default:
                        return chr(octdec($match[1]));
                }
            },
            $value
        );
    }

    /**
     * @param number $dec
     * @see http://php.net/manual/en/function.chr.php#118804
     */
    protected static function unicodeChar($dec): ?string
    {
        if ($dec < 0x80) {
            return chr($dec);
        }

        if ($dec < 0x0800) {
            return chr(0xC0 + ($dec >> 6))
                . chr(0x80 + ($dec & 0x3f));
        }

        if ($dec < 0x010000) {
            return chr(0xE0 + ($dec >> 12))
                . chr(0x80 + (($dec >> 6) & 0x3f))
                . chr(0x80 + ($dec & 0x3f));
        }

        if ($dec < 0x200000) {
            return chr(0xF0 + ($dec >> 18))
                . chr(0x80 + (($dec >> 12) & 0x3f))
                . chr(0x80 + (($dec >> 6) & 0x3f))
                . chr(0x80 + ($dec & 0x3f));
        }

        return null;
    }
}
