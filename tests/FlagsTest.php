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
        
        $flags->add('one', 'two', 'three');

        $this->assertSame(['foo', 'bar', 'one', 'two', 'three'], $flags->jsonSerialize());
        $this->assertCount(5, $flags);
    }

    public function testMergeFlags()
    {
        $flags1 = new Flags('one', 'two', 'three');
        $flags2 = new Flags('three', 'four', 'five');

        $merged = $flags1->mergeWith($flags2);

        $this->assertCount(5, $merged);
        $this->assertSame(['one', 'two', 'three', 'four', 'five'], $merged->toArray());
        $this->assertNotSame($flags1, $merged);
        $this->assertNotSame($flags2, $merged);
    }
}
