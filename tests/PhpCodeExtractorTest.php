<?php

class PhpCodeExtractorTest extends PHPUnit_Framework_TestCase
{
    public function testOne()
    {
        //Extract translations
        $translations = Gettext\Extractors\PhpCode::fromFile(__DIR__.'/files/phpcode.php');

        $this->assertInstanceOf('Gettext\\Translations', $translations);
        $this->assertInstanceOf('Gettext\\Translation', $translations->find('context', 'text 1 with context'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'text 2'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'text 3 (with parenthesis)'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'text 4 "with double quotes"'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'text 5 \'with escaped single quotes\''));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'text 6'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'text 7 (with parenthesis)'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'text 8 "with escaped double quotes"'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'text 9 \'with single quotes\''));
    }

    public function testMultiline()
    {
        //Extract translations
        $translations = Gettext\Extractors\PhpCode::fromFile(__DIR__.'/files/phpcode.php');

        $original = <<<EOT
<div id="blog" class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="content">
                <div class="page_post">
                    <div class="container">
                        <div class="margin-top-40"></div>
                        <div class="col-sm-3 col-md-2 centered-xs an-number">4</div>
                    </div>
                </div>
                <div class="container">
                    <h1 class="text-center margin-top-10">Sorry, but we couldn't find this page</h1>
                    <div id="body-div">
                        <div id="main-div">
                            <div class="text-404">
                                <div>
                                    <p>Maybe you have entered an incorrect URL of the page or page moved to another section or just page is temporarily unavailable.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
EOT;

        $translation = $translations->find(null, $original);

        $this->assertInstanceOf('Gettext\\Translation', $translation);
        $this->assertEquals($original, $translation->getOriginal());
    }
}
