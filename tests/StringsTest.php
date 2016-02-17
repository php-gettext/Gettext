<?php

use Gettext\Utils\Strings;

class StringsTest extends PHPUnit_Framework_TestCase
{
    public function stringFromPhpProvider()
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
            array('"a\x01b\2 \1 \01 \001 \r \n \t \v \f"', "a\1b\2 \1 \1 \1 \r \n \t \v \f"),
            array('"$ \$a \""', '$ $a "'),
        );
    }

    /**
     * @dataProvider stringFromPhpProvider
     */
    public function testStringFromPhp($source, $decoded)
    {
        $this->assertSame($decoded, Strings::fromPhp($source));
    }

    public function poStringsProvider()
    {
        return array(
            array('test', '"test"'),
            array("'test'", '"\'test\'"'),
            array("Special chars: \r \n \t", '"Special chars: \\r \\n \\t"'),
            array("Newline\nSlash and n\\n", '"Newline\nSlash and n\\n"'),
        );
    }

    /**
     * @dataProvider poStringsProvider
     */
    public function testStringToPo($phpString, $poString)
    {
        $this->assertSame($poString, Strings::ToPo($phpString));
    }

    /**
     * @dataProvider poStringsProvider
     */
    public function testStringFromPo($phpString, $poString)
    {
        $this->assertSame($phpString, Strings::fromPo($poString));
    }
}
