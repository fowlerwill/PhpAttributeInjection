<?php

namespace FowlerWill\AttributeInjection\Test\Fixtures;

use FowlerWill\AttributeInjection\Injected;

class TestClassWithInvalidDependency
{
    #[Injected]
    protected \NonExistantClass $prop;

    public function getProp(): \NonExistantClass
    {
        return $this->prop;
    }
}