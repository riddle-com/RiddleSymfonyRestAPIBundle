<?php

namespace Riddle\RestAPIBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Riddle\RestAPIBundle\DependencyInjection\RiddleRestAPIBundleExtension;

class RiddleRestAPIBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new RiddleRestAPIBundleExtension();
    }
}