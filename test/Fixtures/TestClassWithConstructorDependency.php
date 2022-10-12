<?php

namespace FowlerWill\AttributeInjection\Test\Fixtures;

use FowlerWill\AttributeInjection\Injected;

class TestClassWithConstructorDependency
{
    protected TestDependencyInterface $prop;

    public function __construct(
        #[Injected]
        TestDependencyInterface $prop
    ) {
        $this->prop = $prop;        
    }

    public function getProp(): TestDependencyInterface
    {
        return $this->prop;
    }
}