<?php

namespace HJerichen\ClassInstantiator\Test;

use HJerichen\ClassInstantiator\ObjectStore;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        (new ObjectStore())->flush();
    }
}