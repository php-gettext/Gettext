<?php
declare(strict_types = 1);

namespace Gettext\Tests;

use Gettext\Scanner\ParsedFunction;
use PHPUnit\Framework\TestCase;

class ParsedFunctionsAndCommentsTest extends TestCase
{
    public function testParsedFunction(): void
    {
        $function = new ParsedFunction('__', 'template.php', 45);

        $this->assertSame('__', $function->getName());
        $this->assertSame('template.php', $function->getFilename());
        $this->assertSame(45, $function->getLine());
        $this->assertSame(45, $function->getLastLine());

        $function->addArgument('a');
        $this->assertSame(['a'], $function->getArguments());

        $function->addArgument('c');
        $this->assertSame(['a', 'c'], $function->getArguments());

        $function->addComment('This is a comment');
        $this->assertSame(['This is a comment'], $function->getComments());

        $function->addComment('This is other comment');
        $this->assertSame(['This is a comment', 'This is other comment'], $function->getComments());

        $function->addFlag('php-format');
        $this->assertSame(['php-format'], $function->getFlags());
    }
}
