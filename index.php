<?php

function loggy(string $message)
{
    echo $message . "\n\n";
}

class TestDeepDependency
{
    public function __construct()
    {
        loggy('deep dep constructed');
    }
}

class TestDependency
{

    #[Injected]
    protected TestDeepDependency $dep;


    public function __construct()
    {
        loggy("TestDependency was constructed");
    }
    
    public function bar() {
        loggy("bar called");
    }

}

class TestClass
{

    #[Injected]
    protected TestDependency $dep;

    public function __construct()
    {
        loggy("TestClass was constructed");
    }

    public function foo() { $this->dep->bar(); }
}

#[Attribute]
class Injected
{
    public function __construct()
    {
        loggy("Injected was constructed");
    }
}

class Container
{
    static function make(string $className)
    {
        loggy("\nContainer::make was called on $className");

        $object = new $className();
        $reflectionClass = new ReflectionClass($className);

        foreach($reflectionClass->getProperties() as $property) {
            if ($property->getAttributes(Injected::class)) {
                $dependencyType = $property->getType()?->getName();

                $reflectionProperty = new ReflectionProperty($object, $property->getName());
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($object, Container::make($dependencyType));
            }
            
        }

        return $object;
    }
}

$test = Container::make(TestClass::class);

$test->foo();

loggy('done');