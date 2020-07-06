<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassMethodWithParameterWhoHasAnnotation
{
    public function method(ClassWithDependencyOfEnvironmentWithAnnotation $parameter): Environment
    {
        return $parameter->getEnvironment();
    }
}