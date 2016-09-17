<?php

namespace b3da\PusherBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class b3daPusherExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter( 'b3da_pusher.fcm', $config['fcm']);
        $container->setParameter( 'b3da_pusher.fcm.server_url', $config['fcm']['server_url']);
        $container->setParameter( 'b3da_pusher.fcm.server_key', $config['fcm']['server_key']);
        $container->setParameter( 'b3da_pusher.fcm.proxy', $config['fcm']['proxy']);

        $container->setParameter( 'b3da_pusher.gcm', $config['gcm']);
        $container->setParameter( 'b3da_pusher.gcm.server_url', $config['gcm']['server_url']);
        $container->setParameter( 'b3da_pusher.gcm.server_key', $config['gcm']['server_key']);
        $container->setParameter( 'b3da_pusher.gcm.proxy', $config['gcm']['proxy']);

        $container->setParameter( 'b3da_pusher.apn', $config['apn']);
        $container->setParameter( 'b3da_pusher.apn.server_url', $config['apn']['server_url']);
        $container->setParameter( 'b3da_pusher.apn.passphrase', $config['apn']['passphrase']);
        $container->setParameter( 'b3da_pusher.apn.cert_path', $config['apn']['cert_path']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
