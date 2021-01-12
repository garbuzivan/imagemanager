<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager\Pipes;

use Spatie\ImageOptimizer\OptimizerChainFactory;

class Optimizer extends AbstractPipes
{
    /**
     * @param array $file
     * @return array
     */
    public function __invoke(array $file): array
    {
        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->optimize($file['disk']);
        return $file;
    }
}
