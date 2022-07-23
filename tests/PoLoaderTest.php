<?php
declare(strict_types = 1);

namespace Gettext\Tests;

use Gettext\Loader\Loader;
use Gettext\Loader\PoLoader;

class PoLoaderTest extends BasePoLoaderTestCase
{
    protected function createPoLoader(): Loader
    {
        return new PoLoader();
    }

    /**
     * @dataProvider stringDecodeProvider
     * @param mixed $source
     * @param mixed $decoded
     */
    public function testStringDecode($source, $decoded)
    {
        $this->assertSame($decoded, PoLoader::decode($source));
    }
}
