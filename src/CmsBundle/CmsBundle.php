<?php

namespace CmsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use CmsBundle\DependencyInjection\Compiler\WidgetCompilerPass;

class CmsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new WidgetCompilerPass());
    }
}
