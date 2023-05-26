<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Attribute;

use Attribute;

/**
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_PARAMETER)]
class Instantiator
{
    /** @param class-string|null $class */
    public function __construct(
        public ?string $class = null,
        public bool $store = false,
        public ?string $method = null
    ) {
    }
}