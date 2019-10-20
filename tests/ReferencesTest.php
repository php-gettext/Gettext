<?php

namespace Gettext\Tests;

use Gettext\References;
use PHPUnit\Framework\TestCase;

class ReferencesTest extends TestCase
{
    public function testReferences()
    {
        $references = new References();

        $this->assertSame([], $references->jsonSerialize());
        $this->assertCount(0, $references);
        
        $references->add('filename.php', 34);
        
        $this->assertSame(['filename.php' => [34]], $references->jsonSerialize());
        $this->assertCount(1, $references);
        
        $references->add('filename.php', 34);
        
        $this->assertSame(['filename.php' => [34]], $references->jsonSerialize());
        $this->assertCount(1, $references);
        
        $references->add('filename.php', 44);

        $this->assertSame(['filename.php' => [34, 44]], $references->jsonSerialize());
        $this->assertCount(2, $references);

        foreach ($references as $filename => $lines) {
            $this->assertSame('filename.php', $filename);
            $this->assertSame([34, 44], $lines);
        }
    }
}
