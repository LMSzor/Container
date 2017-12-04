# Container
PSR-11 compatible container with basic autowiring implementation.

## Example
```php
// src/SomeSpace/Foo.php
namespace SomeSpace;
class Foo {}

// src/SomeSpace/Bar.php
namespace SomeSpace;
class Bar {
  private $foo;
  public function __construct(Foo $foo) {
    $this->foo = $foo;
  }
}

// public/index.php
$c = new LMSzor\Container\Container();
$bar = $c->get(SomeSpace\Bar::class);
