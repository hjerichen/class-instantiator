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
readonly class ClassWithDependencyOfIntegerClass2
{
    private ClassWithIntegerParameter $object;

    public function __construct(
        #[Instantiator(class: ClassInstantiatorExtended::class)] ClassWithIntegerParameter $object
    ) {
        $this->object = $object;
    }
}