<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("CLASS")
 * @author Heiko Jerichen <heiko@jerichen.de>
 */
class Instantiator
{
    /**
     * @var string
     * @Required
     */
    public $class;
}