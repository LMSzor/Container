<?php

namespace {
    use LMSzor\Container\{ ClassNotFoundInGlobalSpaceException, Container };
    use PHPUnit\Framework\TestCase;
    use TestFixtures\FooBar;
    use TestFixtures\FooBarProvider;

    class ContainerTest extends TestCase {
        /** @var null|Container */
        private $container = null;

        public function setUp() {
            $this->container = new Container();
        }

        public function testGetNonExisting() {
            $this->expectException(ClassNotFoundInGlobalSpaceException::class);
            $this->container->get('nonExisting');
        }

        public function testAddAsClosureAndGetExisting() {
            $entry = new \stdClass();

            $this->container->add(function() use($entry) {
                return $entry;
            });

            $this->assertSame($entry, $this->container->get(get_class($entry)));
        }

        public function testAddAsProvider() {
            $this->container->add(FooBarProvider::class);
            /** @var FooBar $fooBar */
            $fooBar = $this->container->get(FooBar::class);

            $this->assertInstanceOf(FooBar::class, $fooBar);
            $this->assertInstanceOf(\TestFixtures\Foo::class, $fooBar->getFoo());
            $this->assertInstanceOf(\TestFixtures\Bar::class, $fooBar->getBar());
            $this->assertEquals(strtoupper($fooBar->getFoo()->lower), $fooBar->getBar()->upper);
        }
    }
}

namespace TestFixtures {

    use LMSzor\Container\EntryProviderInterface;

    class Foo {
        public $lower = 'qwertyuiop';
    }

    class Bar {
        public $upper;
        private $foo;

        public function __construct(Foo $foo) {
            $this->foo = $foo;
            $this->upper = strtoupper($this->foo->lower);
        }
    }

    class FooBar {
        /** @var null|Foo */
        private $foo = null;

        /** @var null|Bar */
        private $bar = null;

        /**
         * @return null|Foo
         */
        public function getFoo(): Foo {
            return $this->foo;
        }

        /**
         * @param null|Foo $foo
         * @return FooBar
         */
        public function setFoo(Foo $foo): FooBar {
            $this->foo = $foo;
            return $this;
        }

        /**
         * @return null|Bar
         */
        public function getBar(): Bar {
            return $this->bar;
        }

        /**
         * @param null|Bar $bar
         * @return FooBar
         */
        public function setBar(Bar $bar): FooBar {
            $this->bar = $bar;
            return $this;
        }
    }

    class FooBarProvider implements EntryProviderInterface {
        /** @var null|Foo */
        private $foo = null;

        /** @var null|Bar */
        private $bar = null;

        public function __construct(Foo $foo, Bar $bar) {
            $this->foo = $foo;
            $this->bar = $bar;
        }

        public function register() {
            $fooBar = new FooBar();
            $fooBar->setFoo($this->foo);
            $fooBar->setBar($this->bar);

            return $fooBar;
        }
    }
}