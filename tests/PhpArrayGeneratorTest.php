<?php

class PhpArrayGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function testOne()
    {
        //Extract translations
        $translations = Gettext\Extractors\PhpCode::fromFile(__DIR__.'/files/phpcode.php');

        $array = Gettext\Generators\PhpArray::toArray($translations);

        $this->assertTrue(is_array($array));
        $this->assertArrayHasKey('messages', $array);
        $this->assertCount(12, $array['messages']);
    }
}
