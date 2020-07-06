<?php
/** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Test\Helpers;

use HJerichen\ClassInstantiator\Annotation\Instantiator;
use HJerichen\ClassInstantiator\Test\Helpers\SimpleClass;

/**
 * @Instantiator(SimpleClass::class)
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class ClassWithDependencyOfEnvironmentWithNotInstantiatorInAnnotation
{

}