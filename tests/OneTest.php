<?php

use Gettext\Translations;

class PoTest extends AbstractTest
{
    protected static $file = '1';

    const COUNT = 3;

    public function testPo()
    {
        $translations = Translations::fromPoFile(self::file('po'));

        $this->assertCount(self::COUNT, $translations);
        $this->assertCount(12, $translations->getHeaders());
        $this->assertSame(self::content('po'), $translations->toPoString());
    }
}
