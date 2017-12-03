<?php

class ContainerTest extends \PHPUnit\Framework\TestCase {
    public function testContainerCreation() {
        $object = null;
        $this->assertEquals(null, $object);

        $object = new \LMSzor\Container\Container();
        $this->assertInstanceOf(\LMSzor\Container\Container::class, $object);
    }
    public function testAddEntryToContainer() {
        $object = new \LMSzor\Container\Container();
        $this->assertInstanceOf(\LMSzor\Container\Container::class, $object);

        $object->add('TestEntry', function() {
            $testEntry = new stdClass();
            $testEntry->name = 'TestEntry';
            $testEntry->description = 'Why Not?';

            return $testEntry;
        });
    }

    public function testCheckIfContainerDoesNotHaveEntry() {
        $object = new \LMSzor\Container\Container();
        $this->assertInstanceOf(\LMSzor\Container\Container::class, $object);

        $result = $object->has('Some\\Not\\Existing\\Entry');
        $this->assertEquals(false, $result);
    }

    public function testCheckIfContainerHasEntry() {
        $object = new \LMSzor\Container\Container();
        $this->assertInstanceOf(\LMSzor\Container\Container::class, $object);

        $object->add('Some\\Entry\\Name', function() {
            $testEntry = new stdClass();
            $testEntry->data = 'asgfbadblas gasghf asdarg lei';

            return $testEntry;
        });

        $result = $object->has('Some\\Entry\\Name');
        $this->assertEquals(true, $result);
    }

    public function testThrowExceptionWhenGettingNonExistingEntry() {
        $object = new \LMSzor\Container\Container();
        $this->assertInstanceOf(\LMSzor\Container\Container::class, $object);

        $this->expectException(\LMSzor\Container\EntryNotFound::class);
        $object->get('Some\\Not\\Existing\\Entry');
    }

    public function testGetExistingEntry() {
        $object = new \LMSzor\Container\Container();
        $this->assertInstanceOf(\LMSzor\Container\Container::class, $object);

        $object->add('Some\\Entry\\Name', function() {
            $testEntry = new stdClass();
            $testEntry->data = 'asgfbadblas gasghf asdarg lei';

            return $testEntry;
        });

        $result = $object->get('Some\\Entry\\Name');
        $this->assertInstanceOf(stdClass::class, $result);
    }
}