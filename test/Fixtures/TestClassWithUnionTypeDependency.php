<?php

namespace FowlerWill\AttributeInjection\Test\Fixtures;

use FowlerWill\AttributeInjection\Injected;

class TestClassWithUnionTypeDependency
{
    #[Injected]
    protected TestDependency|string $prop;

    public function getProp(): TestDependency|string
    {
        return $this->prop;
    }
}