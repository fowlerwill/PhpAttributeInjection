<?php

namespace FowlerWill\AttributeInjection\Test\Fixtures;

use FowlerWill\AttributeInjection\Injected;

class TestClassWithMissingTypeDependency
{
    #[Injected]
    protected $prop;

    public function getProp()
    {
        return $this->prop;
    }
}