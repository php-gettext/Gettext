<?php

namespace Gettext\Tests;

use Gettext\Headers;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class HeadersTest extends TestCase
{
    public function testHeaders()
    {
        $headers = new Headers();

        $this->assertSame([], $headers->jsonSerialize());
        $this->assertCount(0, $headers);
        
        $headers->set('foo', 'bar');
        
        $this->assertSame(['foo' => 'bar'], $headers->jsonSerialize());
        $this->assertCount(1, $headers);
        $this->assertSame('bar', $headers->get('foo'));
        
        $headers->set('foo', 'bar2');

        $this->assertSame(['foo' => 'bar2'], $headers->jsonSerialize());
        $this->assertCount(1, $headers);
        $this->assertSame('bar2', $headers->get('foo'));
        
        $headers->set('foo2', 'bar2');

        $this->assertSame(['foo' => 'bar2', 'foo2' => 'bar2'], $headers->jsonSerialize());
        $this->assertCount(2, $headers);
        $this->assertSame('bar2', $headers->get('foo2'));

        $headers->delete('foo2');
        $this->assertCount(1, $headers);

        $headers->clear();
        $this->assertCount(0, $headers);
    }

    public function testDomain()
    {
        $headers = new Headers();
        $headers->setDomain('foo');

        $this->assertCount(1, $headers);
        $this->assertSame('X-Domain', Headers::HEADER_DOMAIN);
        $this->assertSame('foo', $headers->get(Headers::HEADER_DOMAIN));
        $this->assertSame('foo', $headers->getDomain());
    }

    public function testLanguage()
    {
        $headers = new Headers();
        $headers->setLanguage('gl_ES');

        $this->assertCount(1, $headers);
        $this->assertSame('Language', Headers::HEADER_LANGUAGE);
        $this->assertSame('gl_ES', $headers->get(Headers::HEADER_LANGUAGE));
        $this->assertSame('gl_ES', $headers->getLanguage());
    }
    
    public function testInvalidLanguage()
    {
        $this->expectException(InvalidArgumentException::class);

        $headers = new Headers();
        $headers->setPluralForm(1, 'foo');
    }

    public function testPluralForm()
    {
        $headers = new Headers();
        $headers->setPluralForm(2, '(n=1)');

        $this->assertCount(1, $headers);
        $this->assertSame('Plural-Forms', Headers::HEADER_PLURAL);
        $this->assertSame('nplurals=2; plural=(n=1);', $headers->get(Headers::HEADER_PLURAL));
        $this->assertSame([2, '(n=1)'], $headers->getPluralForm());
    }
}
