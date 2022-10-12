<?php

namespace FowlerWill\AttributeInjection;

use ReflectionClass;

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
        $reflectedClass = InjectedReflectionClass::make($className);

        $constructorParams = array_map(
            [$this, 'make'], 
            $reflectedClass->getConstructorInjectionTypeNames()
        );

        $concrete = $this->instantiateClass($className, $constructorParams);

        foreach ($reflectedClass->getPropertyInjections() as $reflectedProperty) {
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
    private function instantiateClass(string $className, array $params): object
    {
        if (\interface_exists($className)) {
            if (\array_key_exists($className, $this->interfaceMap)) {
                return new $this->interfaceMap[$className](...$params);
            } else {
                throw ContainerException::classNotExists($className);
            }
        }
        
        return new $className(...$params);
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