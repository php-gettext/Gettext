<?php

namespace Gettext\Utils;

/**
 * Comment parsed by PhpFunctionsScanner.
 */
class ParsedComment
{
    /**
     * The comment itself.
     *
     * @var string
     */
    protected $comment;

    /**
     * The line where the function starts.
     *
     * @var int
     */
    protected $line;

    /**
     * Initializes the instance.
     *
     * @param string $comment The comment itself.
     * @param int    $line The line where the comment starts.
     */
    public function __construct($comment, $line)
    {
        $this->comment = $comment;
        $this->line = $line;
    }

	/**
	 * Return the comment's line number.
	 *
	 * @return int Line number.
	 */
	public function getLine()
	{
		return $this->line;
    }

	/**
	 * Return the actual comment string.
	 *
	 * @return string The comment.
	 */
	public function getComment()
	{
		return $this->comment;
    }
}
