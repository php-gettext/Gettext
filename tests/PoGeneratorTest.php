<?php

class PoGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function testGenerator()
    {
        //Extract translations
        $translations = Gettext\Extractors\Po::fromFile(__DIR__.'/files/po.po');

        $generated = $translations->toPoString();
        $expected = file_get_contents(__DIR__.'/files/expected-po.po');

        //Remove auto-created headers with the current time before compare
        $generated = preg_replace('/"(PO-Revision|POT-Creation)-Date:.*"$/m', '', $generated);
        $expected = preg_replace('/"(PO-Revision|POT-Creation)-Date:.*"$/m', '', $expected);

        $this->assertEquals($expected, $generated);
    }
}
