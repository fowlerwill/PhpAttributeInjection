<?php

namespace FowlerWill\AttributeInjection;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;

class InjectedReflectionClass extends ReflectionClass
{

    public static function make(string $className)
    {
        try {
            return new InjectedReflectionClass($className);
        } catch (ReflectionException $e) {        
            throw ContainerException::classNotExists($className);
        }
    }

    public function getConstructorInjectionTypeNames(): array
    {
        return $this->getInjectionTypeNames($this->getConstructorInjections());
    }

    public function getPropertyInjectionTypeNames(): array
    {
        return $this->getInjectionTypeNames($this->getPropertyInjections());
    }

    /**
     * @return ReflectionParameter[]
     */
    public function getConstructorInjections(): array
    {
        /** @var ReflectionParameter[] */
        $reflectionParams = $this->getConstructor()?->getParameters() ?? [];
        return $this->getInjected($reflectionParams);
    }

    /**
     * @return ReflectionProperty[]
     */
    public function getPropertyInjections(): array
    {
        /** @var ReflectionProperty[] */
        $reflectionParams = $this->getProperties();
        return $this->getInjected($reflectionParams);
    }

    /**
     * @psalm-param array<ReflectionParameter|ReflectionProperty> $reflections
     */
    private function getInjected(array $reflections): array
    {
        $injected = [];

        foreach ($reflections as $reflection) {
            if ($this->isInjected($reflection)) {
                $injected[] = $reflection;
            }
        }

        return $injected;
    }

    /**
     * @psalm-param array<ReflectionParameter|ReflectionProperty> $reflections
     */
    private function getInjectionTypeNames(array $reflections): array
    {
        $typeNames = [];
        foreach ($reflections as $dependency) {
            $type = $dependency->getType();
            if ($type instanceof ReflectionNamedType) {
                $typeNames[] = $type->getName();
            } else {
                throw ContainerException::paramIncorrectType($dependency->getName());
            }
        }
        return $typeNames;
    }

    private function isInjected(ReflectionParameter|ReflectionProperty $reflection)
    {
        return !empty($reflection->getAttributes(Injected::class));
    }
}