<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassMethodWithParameterWhoHasAttribute
{
    public function method(ClassWithDependencyOfEnvironmentWithAttribute $parameter): Environment
    {
        return $parameter->getEnvironment();
    }
}