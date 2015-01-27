<?php

class CloningTest extends PHPUnit_Framework_TestCase
{
    public function testClone()
    {
        $list1 = new Gettext\Translations();
        $item = new Gettext\Translation('', 'Test');
        $list1->append($item);

        $clonedItem = clone $item;
        $this->assertNotSame($item, $clonedItem);

        $list2 = clone $list1;

        $item1 = $list1->find($item);
        $this->assertSame($item, $item1);

        $item2 = $list2->find($item1);
        $this->assertInstanceOf('Gettext\\Translation', $item2);
        $this->assertNotSame($item, $item2);
    }
}
