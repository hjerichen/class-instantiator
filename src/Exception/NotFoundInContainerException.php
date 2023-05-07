<?php declare(strict_types=1);

namespace HJerichen\ClassInstantiator\Exception;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class NotFoundInContainerException extends RuntimeException implements NotFoundExceptionInterface
{
}
