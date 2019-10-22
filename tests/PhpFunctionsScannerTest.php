<?php

namespace Gettext\Tests;

use Gettext\Scanner\PhpFunctionsScanner;
use PHPUnit\Framework\TestCase;

class PhpFunctionsScannerTest extends TestCase
{
    public function testPhpFunctionsExtractor()
    {
        $scanner = new PhpFunctionsScanner();
        $scanner->setDefinedConstants(['ARG_8' => 'arg8']);
        $file = __DIR__.'/assets/functions.php';
        $code = file_get_contents($file);
        $functions = $scanner->scan($code, $file);

        $this->assertCount(11, $functions);

        //fn1
        $function = array_shift($functions);
        $this->assertSame('fn1', $function->getName());
        $this->assertSame(3, $function->countArguments());
        $this->assertSame(['arg1', 'arg2', '3'], $function->getArguments());
        $this->assertSame(4, $function->getLine());
        $this->assertSame(4, $function->getLastLine());
        $this->assertSame($file, $function->getFilename());
        $this->assertCount(0, $function->getComments());

        //fn2
        $function = array_shift($functions);
        $this->assertSame('fn2', $function->getName());
        $this->assertSame(1, $function->countArguments());
        $this->assertSame(5, $function->getLine());
        $this->assertSame(5, $function->getLastLine());
        $this->assertSame($file, $function->getFilename());
        $this->assertCount(0, $function->getComments());

        //fn4
        $function = array_shift($functions);
        $this->assertSame('fn4', $function->getName());
        $this->assertSame(1, $function->countArguments());
        $this->assertSame(['arg4'], $function->getArguments());
        $this->assertSame(6, $function->getLine());
        $this->assertSame(6, $function->getLastLine());
        $this->assertSame($file, $function->getFilename());
        $this->assertCount(0, $function->getComments());

        //fn5
        $function = array_shift($functions);
        $this->assertSame('fn5', $function->getName());
        $this->assertSame(2, $function->countArguments());
        $this->assertSame(['6', '7.5'], $function->getArguments());
        $this->assertSame(6, $function->getLine());
        $this->assertSame(6, $function->getLastLine());
        $this->assertSame($file, $function->getFilename());
        $this->assertCount(0, $function->getComments());

        //fn3
        $function = array_shift($functions);
        $this->assertSame('fn3', $function->getName());
        $this->assertSame(3, $function->countArguments());
        $this->assertSame([null, 'arg5', null], $function->getArguments());
        $this->assertSame(6, $function->getLine());
        $this->assertSame(6, $function->getLastLine());
        $this->assertSame($file, $function->getFilename());
        $this->assertCount(0, $function->getComments());

        //fn6
        $function = array_shift($functions);
        $this->assertSame('fn6', $function->getName());
        $this->assertSame(1, $function->countArguments());
        $this->assertSame([null], $function->getArguments());
        $this->assertSame(7, $function->getLine());
        $this->assertSame(7, $function->getLastLine());
        $this->assertSame($file, $function->getFilename());
        $this->assertCount(0, $function->getComments());

        //fn7
        $function = array_shift($functions);
        $this->assertSame('fn7', $function->getName());
        $this->assertSame(1, $function->countArguments());
        $this->assertSame([null], $function->getArguments());
        $this->assertSame(8, $function->getLine());
        $this->assertSame(8, $function->getLastLine());
        $this->assertSame($file, $function->getFilename());
        $this->assertCount(0, $function->getComments());

        //fn9
        $function = array_shift($functions);
        $this->assertSame('fn9', $function->getName());
        $this->assertSame(1, $function->countArguments());
        $this->assertSame(['arg8'], $function->getArguments());
        $this->assertSame(11, $function->getLine());
        $this->assertSame(11, $function->getLastLine());
        $this->assertSame($file, $function->getFilename());
        $this->assertCount(1, $function->getComments());

        $comments = $function->getComments();
        $comment = array_shift($comments);
        $this->assertSame(10, $comment->getLine());
        $this->assertSame(10, $comment->getLastLine());
        $this->assertSame('ALLOW: This is a comment to fn9', $comment->getComment());


        //fn10
        $function = array_shift($functions);
        $this->assertSame('fn10', $function->getName());
        $this->assertSame(0, $function->countArguments());
        $this->assertSame(13, $function->getLine());
        $this->assertSame(13, $function->getLastLine());
        $this->assertSame($file, $function->getFilename());
        $this->assertCount(1, $function->getComments());

        $comments = $function->getComments();
        $comment = array_shift($comments);
        $this->assertSame(13, $comment->getLine());
        $this->assertSame(13, $comment->getLastLine());
        $this->assertSame('Comment to fn10', $comment->getComment());

        //fn11
        $function = array_shift($functions);
        $this->assertSame('fn11', $function->getName());
        $this->assertSame(2, $function->countArguments());
        $this->assertSame(['arg9', 'arg10'], $function->getArguments());
        $this->assertSame(16, $function->getLine());
        $this->assertSame(16, $function->getLastLine());
        $this->assertSame($file, $function->getFilename());
        $this->assertCount(3, $function->getComments());

        $comments = $function->getComments();
        $comment = array_shift($comments);
        $this->assertSame(15, $comment->getLine());
        $this->assertSame(15, $comment->getLastLine());
        $this->assertSame('Related comment 1', $comment->getComment());

        $comment = array_shift($comments);
        $this->assertSame(16, $comment->getLine());
        $this->assertSame(16, $comment->getLastLine());
        $this->assertSame('ALLOW: Related comment 2', $comment->getComment());

        $comment = array_shift($comments);
        $this->assertSame(16, $comment->getLine());
        $this->assertSame(16, $comment->getLastLine());
        $this->assertSame('Related comment 3', $comment->getComment());

        //fn12
        $function = array_shift($functions);
        $this->assertSame('fn12', $function->getName());
        $this->assertSame(2, $function->countArguments());
        $this->assertSame(['arg11', 'arg12'], $function->getArguments());
        $this->assertSame(20, $function->getLine());
        // There's no reliable way to get the line number where a function is closed :(
        // $this->assertSame(26, $function->getLastLine());
        $this->assertSame(25, $function->getLastLine());
        $this->assertSame($file, $function->getFilename());
        $this->assertCount(4, $function->getComments());

        $comments = $function->getComments();
        $comment = array_shift($comments);
        $this->assertSame(18, $comment->getLine());
        $this->assertSame(19, $comment->getLastLine());
        $this->assertSame("Related comment\nnumber one", $comment->getComment());

        $comment = array_shift($comments);
        $this->assertSame(21, $comment->getLine());
        $this->assertSame(21, $comment->getLastLine());
        $this->assertSame('Related comment 2', $comment->getComment());

        $comment = array_shift($comments);
        $this->assertSame(23, $comment->getLine());
        $this->assertSame(23, $comment->getLastLine());
        $this->assertSame('ALLOW: Related comment 3', $comment->getComment());

        $comment = array_shift($comments);
        $this->assertSame(25, $comment->getLine());
        $this->assertSame(25, $comment->getLastLine());
        $this->assertSame('Related comment 4', $comment->getComment());
    }

    public function testPhpFunctionsScannerWithDisabledComments()
    {
        $scanner = new PhpFunctionsScanner();
        $scanner->includeComments(false);
        $file = __DIR__.'/assets/functions.php';
        $code = file_get_contents($file);
        $functions = $scanner->scan($code, $file);

        $this->assertCount(11, $functions);

        foreach ($functions as $function) {
            $this->assertCount(0, $function->getComments());
        }
    }

    public function testPhpFunctionsScannerWithPrefixedComments()
    {
        $scanner = new PhpFunctionsScanner();
        $scanner->includeComments(['ALLOW:']);
        $file = __DIR__.'/assets/functions.php';
        $code = file_get_contents($file);
        $functions = $scanner->scan($code, $file);

        $this->assertCount(11, $functions);

        //fn12
        $function = $functions[10];
        $this->assertCount(1, $function->getComments());

        $comments = $function->getComments();
        $comment = $comments[0];
        $this->assertSame(23, $comment->getLine());
        $this->assertSame(23, $comment->getLastLine());
        $this->assertSame('ALLOW: Related comment 3', $comment->getComment());
    }

    public function stringDecodeProvider()
    {
        return [
            ['"test"', 'test'],
            ["'test'", 'test'],
            ["'DATE \a\\t TIME'", 'DATE \a\t TIME'],
            ["'DATE \a\\t TIME$'", 'DATE \a\t TIME$'],
            ["'DATE \a\\t TIME\$'", 'DATE \a\t TIME$'],
            ["'DATE \a\\t TIME\$a'", 'DATE \a\t TIME$a'],
            ['"FIELD\\tFIELD"', "FIELD\tFIELD"],
            ['"$"', '$'],
            ['"Hi $"', 'Hi $'],
            ['"$ hi"', '$ hi'],
            ['"Hi\t$name"', "Hi\t\$name"],
            ['"Hi\\\\"', 'Hi\\'],
            ['"{$obj->name}"', '{$obj->name}'],
            ['"a\x20b $c"', 'a b $c'],
            ['"a\x01b\2 \1 \01 \001 \r \n \t \v \f"', "a\1b\2 \1 \1 \1 \r \n \t \v \f"],
            ['"$ \$a \""', '$ $a "'],
        ];
    }

    /**
     * @dataProvider stringDecodeProvider
     */
    public function testStringDecode($source, $decoded)
    {
        $this->assertSame($decoded, PhpFunctionsScanner::decode($source));
    }
}
