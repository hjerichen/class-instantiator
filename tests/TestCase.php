<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test;

use HJerichen\ClassInstantiator\ObjectStore;

class TestCase extends \PHPUnit\Framework\TestCase
{
    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
        (new ObjectStore())->flush();
    }
}