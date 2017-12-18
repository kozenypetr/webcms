<?php
namespace CmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class WidgetCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('cms.manager.widget')) {
            return;
        }

        $definition = $container->findDefinition(
            'cms.manager.widget'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'cms.widget'
        );
        
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addWidget',
                array($id, new Reference($id))
            );
        }
    }
}