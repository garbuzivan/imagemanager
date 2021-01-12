<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager\Pipes;

abstract class AbstractPipes
{
    abstract public function __invoke(array $file): array;
}
