<?php

namespace b3da\PusherBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('b3da_pusher');

        $rootNode
            ->children()
                ->arrayNode('fcm')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('server_url')
                            ->defaultValue('https://fcm.googleapis.com/fcm/send')
                            ->end()
                        ->scalarNode('server_key')
                            ->defaultValue(null)
                            ->end()
                        ->scalarNode('proxy')
                            ->defaultValue(null)
                            ->end()
                    ->end()
                ->end()
                ->arrayNode('gcm')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('server_url')
                            ->defaultValue('https://android.googleapis.com/gcm/send')
                            ->end()
                        ->scalarNode('server_key')
                            ->defaultValue(null)
                            ->end()
                        ->scalarNode('proxy')
                            ->defaultValue(null)
                            ->end()
                    ->end()
                ->end()
                ->arrayNode('apn')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('server_url')
                            ->defaultValue('ssl://gateway.sandbox.push.apple.com:2195')
                            ->end()
                        ->scalarNode('passphrase')
                            ->defaultValue(null)
                            ->end()
                        ->scalarNode('cert_path')
                            ->defaultValue(null)
                            ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
