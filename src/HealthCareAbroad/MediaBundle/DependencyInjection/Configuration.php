<?php
namespace HealthCareAbroad\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('media');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
/*        
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('length')->defaultValue(5)->end()
            ->scalarNode('width')->defaultValue(130)->end()
            ->scalarNode('height')->defaultValue(50)->end()
            ->scalarNode('font')->defaultValue(__DIR__.'/../Generator/Font/captcha.ttf')->end()
            ->scalarNode('keep_value')->defaultValue(false)->end()
            ->scalarNode('charset')->defaultValue('abcdefhjkmnprstuvwxyz23456789')->end()
            ->scalarNode('as_file')->defaultValue(false)->end()
            ->scalarNode('as_url')->defaultValue(false)->end()
            ->scalarNode('reload')->defaultValue(false)->end()
            ->scalarNode('image_folder')->defaultValue('captcha')->end()
            ->scalarNode('web_path')->defaultValue('%kernel.root_dir%/../web')->end()
            ->scalarNode('gc_freq')->defaultValue(100)->end()
            ->scalarNode('expiration')->defaultValue(60)->end()
            ->scalarNode('quality')->defaultValue(30)->end()
        ->end();
*/              
        
        
        return $treeBuilder;
    }
}

