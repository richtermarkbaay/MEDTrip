<?php
namespace HealthCareAbroad\PagerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PagerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('adapters.xml');

        if($config['limit']) {
            $container->setParameter('chromedia_pager.pager.limit', $config['limit']);
        }
        if($config['max_pages']) {
            $container->setParameter('chromedia_pager.pager.max_pages', $config['max_pages']);
        }
        if($config['template']) {
            $container->setParameter('chromedia_pager.pager.template', $config['template']);
        }

        $pagerDef = $container->getDefinition('chromedia_pager.pager');
        $pagerDef->replaceArgument(0, null);

        // Add concrete pagers based on the same defintion (and defaults) as the main pager class
        $doctrineOrmPagerDef = clone $pagerDef;
        $doctrineOrmPagerDef->replaceArgument(0, new Reference('chromedia_pager.adapter.doctrine_orm'));
        $container->setDefinition('chromedia_pager.doctrine_orm_pager', $doctrineOrmPagerDef);

        $arrayPagerDef = clone $pagerDef;
        $arrayPagerDef->replaceArgument(0, new Reference('chromedia_pager.adapter.array'));
        $container->setDefinition('chromedia_pager.array_pager', $arrayPagerDef);
    }
}
