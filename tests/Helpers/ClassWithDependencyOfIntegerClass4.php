<?php
/** @noinspection UnusedConstructorDependenciesInspection */
/** @noinspection PhpPropertyOnlyWrittenInspection */
declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

use HJerichen\ClassInstantiator\Attribute\Instantiator;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 * @psalm-suppress UnusedProperty
 */
class ClassWithDependencyOfIntegerClass4
{
    private readonly ClassWithIntegerParameter $object;

    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        #[Instantiator(class: ClassInstantiatorExtended::class, method: 'asdsdasd')] ClassWithIntegerParameter $object
    ) {
        $this->object = $object;
    }
}