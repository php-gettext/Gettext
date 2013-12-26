<?php
include dirname(__DIR__).'/Gettext/autoloader.php';

class GettextTest extends PHPUnit_Framework_TestCase {
	
	public function testPhpCodeExtractor () {
		//Extract entries
		$entries = Gettext\Extractors\PhpCode::extract(__DIR__.'/files/phpCode-example.php');

		$this->assertInstanceOf('Gettext\\Entries', $entries);

		return $entries;
	}


	/**
	 * @depends testPhpCodeExtractor
	 */
	public function testEntries ($entries) {
		//Find by text
		$translation = $entries->find(null, 'text 1');

		$this->assertInstanceOf('Gettext\\Translation', $translation);

		//Find by translation object
		$translation2 = $entries->find($translation);

		$this->assertEquals($translation, $translation2);

		//Insert a new translation
		$entries->insert('my context', 'comment', 'comments');

		$commentTranslation = $entries->find('my context', 'comment', 'comments');

		$this->assertInstanceOf('Gettext\\Translation', $commentTranslation);

		$this->assertEquals('comment', $commentTranslation->getOriginal());
		$this->assertEquals('', $commentTranslation->getTranslation());
		$this->assertEquals('my context', $commentTranslation->getContext());
		$this->assertEquals('comments', $commentTranslation->getPlural());
		$this->assertTrue($commentTranslation->hasPlural());

		//Headers
		$entries->setHeader('POT-Creation-Date', '2012-08-07 13:03+0100');
		$this->assertEquals('2012-08-07 13:03+0100', $entries->getHeader('POT-Creation-Date'));

		return $entries;
	}

	/**
	 * @depends testEntries
	 */
	public function testTranslation ($entries) {
		$translation = $entries->find(null, 'text 1');

		$this->assertEquals('text 1', $translation->getOriginal());
		$this->assertEquals('', $translation->getTranslation());
		$this->assertEquals('', $translation->getContext());
		$this->assertEquals('', $translation->getPlural());
		$this->assertFalse($translation->hasPlural());

		//References
		$references = $translation->getReferences();
		$this->assertCount(1, $references);
		$this->assertTrue($translation->hasReferences());
		
		list($filename, $line) = $references[0];

		$this->assertEquals(2, $line);
		$this->assertEquals(__DIR__.'/files/phpCode-example.php', $filename);

		$translation->wipeReferences();
		$this->assertCount(0, $translation->getReferences());

		//Comments
		$this->assertFalse($translation->hasComments());
		
		$translation->addComment('This is a comment');
		
		$this->assertTrue($translation->hasComments());
		$this->assertCount(1, $translation->getComments());
		
		$comments = $translation->getComments();
		$this->assertEquals('This is a comment', $comments[0]);

		//Plurals
		$this->assertFalse($translation->hasPlural());
		
		$translation->setPlural('texts 1');
		$this->assertTrue($translation->hasPlural());

		$this->assertTrue($translation->is('', 'text 1', 'texts 1'));

		$translation->setPluralTranslation('textos 1');

		$this->assertCount(1, $translation->getPluralTranslation());
		$this->assertEquals('textos 1', $translation->getPluralTranslation(0));

		return $entries;
	}


	/**
	 * @depends testTranslation
	 */
	public function testPhpArrayGenerator ($entries) {
		//Export to a file
		$filename = __DIR__.'/files/tmp-phparray.php';

		$result = Gettext\Generators\PhpArray::generateFile($entries, $filename);

		$this->assertTrue($result);
		$this->assertTrue(is_file($filename));

		//Load the data as an array
		$array = include $filename;

		$this->assertTrue(is_array($array));
		$this->assertArrayHasKey('messages', $array);

		//Load the data as entries object
		$entries2 = Gettext\Extractors\PhpArray::extract($filename);

		//Compare the length of the translations in the array an in the entries (the array always has one more message)
		$this->assertEquals(count($array['messages']) - 1, count($entries2));

		unlink($filename);
	}
}