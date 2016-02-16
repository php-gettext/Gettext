<?php

class JedGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function testOne()
    {
        //Extract translations
        $translations = Gettext\Extractors\Po::fromFile(__DIR__.'/files/po.po');

        $jed = $translations->toJedString();
        $expect = file_get_contents(__DIR__.'/files/jed.json');

        $this->assertEquals(json_decode($jed), json_decode($expect));

        $jed2 = Gettext\Extractors\Jed::fromString($expect);

        $this->assertEquals($translations->count(), $jed2->count());
    }
}
