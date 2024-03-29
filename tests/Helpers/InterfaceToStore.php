<?php

namespace HJerichen\ClassInstantiator\Test\Helpers;

use HJerichen\ClassInstantiator\Attribute\Instantiator;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
#[Instantiator(class: ClassInstantiatorExtended::class, store: true)]
interface InterfaceToStore
{

}