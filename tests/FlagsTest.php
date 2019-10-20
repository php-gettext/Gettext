<?php

namespace Gettext\Tests;

use Gettext\Flags;
use PHPUnit\Framework\TestCase;

class FlagsTest extends TestCase
{
    public function testFlags()
    {
        $flags = new Flags();

        $this->assertSame([], $flags->jsonSerialize());
        $this->assertCount(0, $flags);
        
        $flags->add('foo');
        
        $this->assertSame(['foo'], $flags->jsonSerialize());
        $this->assertCount(1, $flags);
        
        $flags->add('foo');
        
        $this->assertSame(['foo'], $flags->jsonSerialize());
        $this->assertCount(1, $flags);
        
        $flags->add('bar');

        $this->assertSame(['foo', 'bar'], $flags->jsonSerialize());
        $this->assertCount(2, $flags);
    }
}
