<?php

namespace HJerichen\ClassInstantiator\Test\Helpers;

use HJerichen\ClassInstantiator\Attribute\Instantiator;
use HJerichen\ClassInstantiator\ClassInstantiator;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
#[Instantiator(class: ClassInstantiator::class, store: true)]
class ClassForExtensionHasHigherPriorityThenAttribute
{

}