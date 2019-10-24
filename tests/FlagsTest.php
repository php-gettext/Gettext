<?php

namespace Gettext\Tests;

use Gettext\Flags;
use PHPUnit\Framework\TestCase;

class FlagsTest extends TestCase
{
    public function testFlags()
    {
        $flags = new Flags();

        $this->assertSame([], $flags->toArray());
        $this->assertCount(0, $flags);
        
        $flags->add('foo');
        
        $this->assertSame(['foo'], $flags->toArray());
        $this->assertCount(1, $flags);
        
        $flags->add('foo');
        
        $this->assertSame(['foo'], $flags->toArray());
        $this->assertCount(1, $flags);
        
        $flags->add('bar');

        $this->assertSame(['foo', 'bar'], $flags->toArray());
        $this->assertCount(2, $flags);
        
        $flags->add('one', 'two', 'three');

        $this->assertSame(['foo', 'bar', 'one', 'two', 'three'], $flags->toArray());
        $this->assertCount(5, $flags);
    }

    public function testMergeFlags()
    {
        $flags1 = new Flags('one', 'two', 'three');
        $flags2 = new Flags('three', 'four', 'five');

        $merged = $flags1->mergeWith($flags2);

        $this->assertCount(5, $merged);
        $this->assertSame(['one', 'two', 'three', 'four', 'five'], $merged->toArray());

        $this->assertNotSame($merged, $flags1);
        $this->assertNotSame($merged, $flags2);
    }
}
