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
     * @throws \InvalidArgumentException
     */
    public function make(string $className)
    {
        /** @var T */
        $concrete = $this->instantiateClass($className);

        foreach ($this->propertiesToInject($concrete::class) as $reflectedProperty) {
            $this->setProperty($concrete, $reflectedProperty);
        }

        return $concrete;
    }

    /**
     * @template T
     * @psalm-param class-string<T> $className
     * @return T
     */
    private function instantiateClass(string $className)
    {
        if (\class_exists($className)) {
            return new $className;
        }
        if (\interface_exists($className) && \array_key_exists($className, $this->interfaceMap)) {
            return new $this->interfaceMap[$className]();
        }
        
        throw new \InvalidArgumentException("Class \"$className\" does not exist.");
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

    private function setProperty(object $concrete, \ReflectionProperty $reflectedProperty): void
    {
        
        if (!$reflectedProperty->hasType() || !$reflectedProperty->getType() instanceof \ReflectionNamedType) {
            throw new \InvalidArgumentException("Property {$reflectedProperty->getName()} requires a single named type.");
        }

        $dependencyClassName = $reflectedProperty->getType()->getName();

        $concreteReflectionProperty = new \ReflectionProperty($concrete, $reflectedProperty->getName());
        $concreteReflectionProperty->setAccessible(true);
        /**
         * @psalm-suppress ArgumentTypeCoercion
         */
        $concreteReflectionProperty->setValue($concrete, $this->make($dependencyClassName));
    }
}