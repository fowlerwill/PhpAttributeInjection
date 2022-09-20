<?php

namespace FowlerWill\AttributeInjection\Test;

use FowlerWill\AttributeInjection\Container;
use FowlerWill\AttributeInjection\Test\Fixtures\ {
    TestClassWithDependency,
    TestDependency,
    TestDependencyInterface
};
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ContainerTest extends TestCase
{

    public function testCreate(): void
    {
        $this->assertInstanceOf(Container::class, new Container());
    }

    public function testSimpleProperty(): void
    {
        $container = new Container([
            TestDependencyInterface::class => TestDependency::class
        ]);
        $prop = $container->make(TestClassWithDependency::class)->getProp();
        $this->assertInstanceOf(TestDependencyInterface::class, $prop);
    }
}