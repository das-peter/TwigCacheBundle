<?php

namespace EmanueleMinotto\TwigCacheBundle\DependencyInjection;

use EmanueleMinotto\TwigCacheBundle\Twig\ProfilerExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class TwigCacheExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config    an array of configuration values
     * @param ContainerBuilder $container a ContainerBuilder instance
     *
     * @throws \InvalidArgumentException if tag is not defined
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $config);

        $container->setAlias('twig_cache.service', $config['service']);
        $container->setAlias('twig_cache.strategy.key_generator', $config['key_generator']);

        $loader = new Loader\XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.xml');

        $container->getDefinition('twig_cache.extension.parent')->replaceArgument(0, new Reference($config['strategy']));
        $container->getDefinition('twig_cache.extension')->replaceArgument(0, new Reference($config['strategy']));

        if ($config['profiler']) {
            $container->setParameter('twig_cache.extension.class', ProfilerExtension::class);

            $container->getDefinition('twig_cache.extension')->addTag('data_collector', [
                'id' => 'asm89_cache',
                'template' => 'TwigCacheBundle:Collector:asm89_cache',
            ]);
        }
    }
}
