<?php

namespace Gettext\Utils;

/**
 * Function parsed by PhpFunctionsScanner.
 */
class ParsedFunction
{
    /**
     * The function name.
     *
     * @var string
     */
    protected $name;

    /**
     * The line where the function starts.
     * 
     * @var int
     */
    protected $line;

    /**
     * The strings extracted from the function arguments.
     *
     * @var string[]
     */
    protected $arguments;

    /**
     * The current index of the function (-1 if no arguments).
     *
     * @var int|null
     */
    protected $argumentIndex;

    /**
     * Shall we stop adding string chunks to the current argument?
     *
     * @var bool
     */
    protected $argumentStopped;

    /**
     * Extracted comments.
     *
     * @var string[]|null
     */
    protected $comments;

    /**
     * Initializes the instance.
     *
     * @param string $name The function name.
     * @param int    $line The line where the function starts.
     */
    public function __construct($name, $line)
    {
        $this->name = $name;
        $this->line = $line;
        $this->arguments = array();
        $this->argumentIndex = -1;
        $this->argumentStopped = false;
        $this->comments = null;
    }

    /**
     * Stop extracting strings from the current argument (because we found something that's not a string).
     */
    public function stopArgument()
    {
        if ($this->argumentIndex === -1) {
            $this->argumentIndex = 0;
        }
        $this->argumentStopped = true;
    }

    /**
     * Go to the next argument because we a comma was found.
     */
    public function nextArgument()
    {
        if ($this->argumentIndex === -1) {
            // This should neve occur, but let's stay safe - During test/development an Exception should be thrown.
            $this->argumentIndex = 1;
        } else {
            ++$this->argumentIndex;
        }
        $this->argumentStopped = false;
    }

    /**
     * Add a string to the current argument.
     *
     * @param string $chunk
     */
    public function addArgumentChunk($chunk)
    {
        if ($this->argumentStopped === false) {
            if ($this->argumentIndex === -1) {
                $this->argumentIndex = 0;
            }
            if (isset($this->arguments[$this->argumentIndex])) {
                $this->arguments[$this->argumentIndex] .= $chunk;
            } else {
                $this->arguments[$this->argumentIndex] = $chunk;
            }
        }
    }

    /**
     * Add a comment associated to this function.
     *
     * @param string $comment
     */
    public function addComment($comment)
    {
        if ($this->comments === null) {
            $this->comments = array();
        }
        $this->comments[] = $comment;
    }
    /**
     * A closing parenthesis was found: return the final data.
     *
     * @return array{
     *
     *   @var string The function name.
     *   @var int The line where the function starts.
     *   @var string[] the strings extracted from the function arguments.
     *   @var string[] the comments associated to the function.
     * }
     */
    public function close()
    {
        $arguments = array();
        for ($i = 0; $i <= $this->argumentIndex; ++$i) {
            $arguments[$i] = isset($this->arguments[$i]) ? $this->arguments[$i] : '';
        }

        return array(
            $this->name,
            $this->line,
            $arguments,
            $this->comments,
        );
    }
}
