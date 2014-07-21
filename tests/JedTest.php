<?php
include dirname(__DIR__).'/Gettext/autoloader.php';

if (!function_exists('n__')) {
    require_once(__DIR__ . '/../Gettext/translator_functions.php');
}

class JedTest extends PHPUnit_Framework_TestCase
{
    public function testDynamicHeaders() {
        $translations = Gettext\Extractors\Po::extract(__DIR__.'/files/gettext_multiple_headers.po');
        $jsonString = Gettext\Generators\Jed::generate($translations, __DIR__.'/files/gettext_multiple_headers.json', true);

        $domain = 'testingdomain';
        $jedJson = json_decode($jsonString, true);
        $this->assertTrue(!empty($jedJson[$domain]) && $jedJson[$domain]['']['domain'] === $domain, "Domain '$domain' either missing or invalid");

        $language = 'bs';
        $this->assertEquals($language, $jedJson[$domain]['']['lang'], 'language does not match expected');
    }

    public function testMultipleDomains() {
        $bsTrans = Gettext\Extractors\Po::extract(__DIR__.'/files/gettext_multiple_headers.po');
        $stdTrans = Gettext\Extractors\Po::extract(__DIR__.'/files/gettext_plural.po');

        $jedJson = Gettext\Generators\Jed::generateMultipleDomains($bsTrans, $stdTrans, false);
        $this->assertNotEmpty($jedJson['testingdomain'], 'testing domain not found');
        $this->assertNotEmpty($jedJson['messages'], 'default domain');
    }
}
