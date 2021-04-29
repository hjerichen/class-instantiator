<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Attribute;

use Attribute;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Instantiator
{
    public function __construct(
        public string $class,
        public bool $store = false
    ) {
    }
}