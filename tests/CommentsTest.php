<?php

namespace Gettext\Tests;

use Gettext\Comments;
use PHPUnit\Framework\TestCase;

class CommentsTest extends TestCase
{
    public function testComments()
    {
        $comments = new Comments();

        $this->assertSame([], $comments->jsonSerialize());
        $this->assertCount(0, $comments);
        
        $comments->add('foo');
        
        $this->assertSame(['foo'], $comments->jsonSerialize());
        $this->assertCount(1, $comments);
        
        $comments->add('foo');
        
        $this->assertSame(['foo'], $comments->jsonSerialize());
        $this->assertCount(1, $comments);
        
        $comments->add('bar');

        $this->assertSame(['foo', 'bar'], $comments->jsonSerialize());
        $this->assertCount(2, $comments);
    }
}
