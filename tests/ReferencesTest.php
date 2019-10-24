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

    public function testMergeReferences()
    {
        $references1 = new References();
        $references2 = new References();

        $references1
            ->add('filename.php', 34)
            ->add('filename.php', 56)
            ->add('filename3.php')
            ->add('filename2.php', 10);

        $references2
            ->add('filename.php', 34)
            ->add('filename.php', 44)
            ->add('filename2.php')
            ->add('filename4.php')
            ->add('filename3.php', 10);
        
        $merged = $references1->mergeWith($references2);

        $this->assertCount(6, $merged);
        $this->assertSame([
            'filename.php' => [34, 56, 44],
            'filename3.php' => [10],
            'filename2.php' => [10],
            'filename4.php' => [],
        ], $merged->toArray());

        $this->assertNotSame($merged, $references1);
        $this->assertNotSame($merged, $references2);
    }
}
