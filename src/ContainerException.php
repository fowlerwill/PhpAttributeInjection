<?php

namespace FowlerWill\AttributeInjection;

class ContainerException extends \Exception
{
    /**
     * @psalm-param class-string $className
     */
    public static function classNotExists(string $className): ContainerException
    {
        return new ContainerException("Class $className does not exist.");
    }


    public static function propertyRequiresType(string $propertyName): ContainerException
    {
        return new ContainerException("Property $propertyName requires a type.");
    }


    public static function propertyRequiresSingleType(string $propertyName): ContainerException
    {
        return new ContainerException("Property $propertyName requires a single, named type.");
    }
}
