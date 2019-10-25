<?php

namespace Gettext\Tests;

use Gettext\Scanner\ParsedFunction;
use Gettext\Scanner\ParsedComment;
use PHPUnit\Framework\TestCase;

class ParsedFunctionsAndCommentsTest extends TestCase
{
    public function testParsedFunction()
    {
        $function = new ParsedFunction('__', 'template.php', 45);

        $this->assertSame('__', $function->getName());
        $this->assertSame('template.php', $function->getFilename());
        $this->assertSame(45, $function->getLine());
        $this->assertSame(45, $function->getLastLine());
        
        $function->setLastLine(46);
        $this->assertSame(46, $function->getLastLine());
        
        $function->addArgument('a');
        $this->assertSame(['a'], $function->getArguments());
        
        $function->addArgumentChunk('b');
        $this->assertSame(['ab'], $function->getArguments());
        
        $function->addArgument('c');
        $this->assertSame(['ab', 'c'], $function->getArguments());
        
        $function->addArgumentChunk('d');
        $this->assertSame(['ab', 'cd'], $function->getArguments());
        
        $comment = new ParsedComment('This is a comment', 'template.php', 44);
        $function->addComment($comment);
        $this->assertSame([$comment], $function->getComments());
        
        $comment2 = new ParsedComment('This is other comment', 'template.php', 44);
        $function->addComment($comment2);
        $this->assertSame([$comment, $comment2], $function->getComments());
    }

    public function testComment()
    {
        $comment = new ParsedComment('Comment', 'template.php', 44);

        $this->assertSame('Comment', $comment->getComment());
        $this->assertSame('template.php', $comment->getFilename());
        $this->assertSame(44, $comment->getLine());
        $this->assertSame(44, $comment->getLastLine());
    }

    public function testMultilineComment()
    {
        $text = <<<EOT
/**
 * This is a multiline
 * comment
 */
EOT;

        $expectedText = <<<EOT
This is a multiline
comment
EOT;

        $comment = new ParsedComment($text, 'template.php', 44);

        $this->assertSame($expectedText, $comment->getComment());
        $this->assertSame('template.php', $comment->getFilename());
        $this->assertSame(44, $comment->getLine());
        $this->assertSame(47, $comment->getLastLine());
    }

    public function testRelationsFunctionsAndComments()
    {
        $function = new ParsedFunction('__', 'template.php', 45);
        $function->setLastLine(46);
        
        $comment = new ParsedComment('Comment', 'template.php', 43);
        $this->assertFalse($comment->isRelatedWith($function));

        $comment = new ParsedComment('Comment', 'template.php', 44);
        $this->assertTrue($comment->isRelatedWith($function));

        $comment = new ParsedComment('Comment', 'template.php', 45);
        $this->assertTrue($comment->isRelatedWith($function));

        $comment = new ParsedComment('Comment', 'template.php', 46);
        $this->assertTrue($comment->isRelatedWith($function));

        $comment = new ParsedComment('Comment', 'template.php', 47);
        $this->assertFalse($comment->isRelatedWith($function));
    }
}
