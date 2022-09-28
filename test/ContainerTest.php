<?php

namespace FowlerWill\AttributeInjection\Test;

use FowlerWill\AttributeInjection\{
    Container,
    ContainerException
};
use FowlerWill\AttributeInjection\Test\Fixtures\{
    TestClassWithDependency,
    TestClassWithMissingTypeDependency,
    TestClassWithUnionTypeDependency,
    TestClassWithInvalidDependency,
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

    public function testBadProperty(): void
    {
        $container = new Container();
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage("Class NonExistantClass does not exist.");
        $prop = $container->make(TestClassWithInvalidDependency::class);
    }

    public function testMissingInterface(): void
    {
        $container = new Container();
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage("Class " . TestDependencyInterface::class . " does not exist.");
        $prop = $container->make(TestClassWithDependency::class);
    }

    public function testMissingPropertyType(): void
    {
        $container = new Container();
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage("Property prop requires a type.");
        $prop = $container->make(TestClassWithMissingTypeDependency::class);
    }

    public function testUnionPropertyType(): void
    {
        $container = new Container();
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage("Property prop requires a single, named type.");
        $prop = $container->make(TestClassWithUnionTypeDependency::class);
    }
}
