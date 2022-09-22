<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    // This override method meed to fix trouble in local server homestead
    public function getCacheDir() : string
    {
        if (in_array($this->environment, ['dev', 'test'])) {
            return '/tmp/cache/' .  $this->environment;
        }

        return parent::getCacheDir();
    }
}
