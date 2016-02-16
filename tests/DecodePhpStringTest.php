<?php

class DecodePhpStringTest extends PHPUnit_Framework_TestCase
{
    public function decodeStringsProvider()
    {
        return array(
            array('"test"', 'test'),
            array("'test'", 'test'),
            array("'DATE \a\\t TIME'", 'DATE \a\t TIME'),
            array("'DATE \a\\t TIME$'", 'DATE \a\t TIME$'),
            array("'DATE \a\\t TIME\$'", 'DATE \a\t TIME$'),
            array("'DATE \a\\t TIME\$a'", 'DATE \a\t TIME$a'),
            array('"FIELD\\tFIELD"', "FIELD\tFIELD"),
            array('"$"', '$'),
            array('"Hi $"', 'Hi $'),
            array('"$ hi"', '$ hi'),
            array('"Hi\t$name"', "Hi\t\$name"),
            array('"Hi\\\\"', 'Hi\\'),
            array('"{$obj->name}"', '{$obj->name}'),
            array('"a\x20b $c"', 'a b $c'),
        );
    }

    /**
     * @dataProvider decodeStringsProvider
     */
    public function testDecodeStrings($source, $decoded)
    {
        $this->assertSame($decoded, Gettext\Utils\PhpFunctionsScanner::decodeString($source));
    }
}
