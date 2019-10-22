<?php

namespace Gettext\Tests;

use Gettext\Scanner\PhpScanner;
use Gettext\Translations;
use PHPUnit\Framework\TestCase;

class PhpScannerTest extends TestCase
{
    public function testPhpCodeScanner()
    {
        $file = __DIR__.'/assets/code.php';

        $scanner = new PhpScanner(
            new Translations('domain1'),
            new Translations('domain2'),
            new Translations('domain3'),
        );

        $this->assertCount(3, $scanner->getTranslations());

        $scanner->getFunctionsScanner()->setDefinedConstants(['CONTEXT' => 'messages']);
        
        $scanner->scanFile($file);

        list($domain1, $domain2, $domain3) = $scanner->getTranslations();

        $this->assertCount(6, $domain1);
        $this->assertCount(4, $domain2);
        $this->assertCount(1, $domain3);

        $scanner->setDefaultDomain('domain1');
        $scanner->scanFile($file);

        $this->assertCount(39, $domain1);
        $this->assertCount(4, $domain2);
        $this->assertCount(1, $domain3);
    }
}