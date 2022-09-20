<?php

namespace FowlerWill\AttributeInjection\Test\Fixtures;

use FowlerWill\AttributeInjection\Injected;

class TestClassWithDependency
{
    #[Injected]
    protected TestDependencyInterface $prop;

    public function getProp(): TestDependencyInterface
    {
        return $this->prop;
    }
}