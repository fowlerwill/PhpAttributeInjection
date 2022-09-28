<?php

namespace FowlerWill\AttributeInjection;

class Container
{
    /**
     * @psalm-var array<class-string, class-string>
     */
    private array $interfaceMap;

    /**
     * @psalm-param array<class-string, class-string> $interfaceMap
     */
    public function __construct(array $interfaceMap = [])
    {
        $this->interfaceMap = $interfaceMap;
    }

    /**
     * @template T
     * @psalm-param class-string<T> $className
     * @return T
     * @throws ContainerException
     */
    public function make(string $className)
    {
        /** @psalm-var T */
        $concrete = $this->instantiateClass($className);

        foreach ($this->propertiesToInject(get_class($concrete)) as $reflectedProperty) {
            $propertyClassName = $this->getPropertyClassName($reflectedProperty);
            
            $concreteReflectionProperty = new \ReflectionProperty($concrete, $reflectedProperty->getName());
            $concreteReflectionProperty->setAccessible(true);
            $concreteReflectionProperty->setValue($concrete, $this->make($propertyClassName));
        }

        return $concrete;
    }

    /**
     * @psalm-param class-string $className
     */
    private function instantiateClass(string $className): object
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        if (\class_exists($className)) {
            return new $className();
        }

        if (\interface_exists($className) && \array_key_exists($className, $this->interfaceMap)) {
            return new $this->interfaceMap[$className]();
        }
        
        throw ContainerException::classNotExists($className);
    }

    /**
     * @psalm-param class-string $className
     * @return array<\ReflectionProperty>
     */
    private function propertiesToInject(string $className): array
    {
        $reflectedClass = new \ReflectionClass($className);

        $properties = [];
        
        foreach ($reflectedClass->getProperties() as $property) {
            if (!empty($property->getAttributes(Injected::class))) {
                $properties[] = $property;
            }
        }

        return $properties;
    }

    /**
     * @psalm-return class-string
     */
    private function getPropertyClassName(\ReflectionProperty $reflectedProperty): string
    {
        $name = $reflectedProperty->getName() ?: "UNKNOWN_PROPERTY";

        if (!$reflectedProperty->hasType() ) {
            throw ContainerException::propertyRequiresType($name);
        }
        
        if (!$reflectedProperty->getType() instanceof \ReflectionNamedType) {
            throw ContainerException::propertyRequiresSingleType($name);
        }
        
        /** @psalm-var class-string */
        return $reflectedProperty->getType()->getName();
    }
}