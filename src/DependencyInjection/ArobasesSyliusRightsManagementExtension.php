<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class ArobasesSyliusRightsManagementExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
        $container->setParameter('arobases_sylius_rights_management', $config['groups']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        // Explicit mapping so the entities are loaded even when doctrine "auto_mapping" is disabled
        $container->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => [
                    'ArobasesSyliusRightsManagementPlugin' => [
                        'type' => 'attribute',
                        'is_bundle' => false,
                        'dir' => __DIR__ . '/../Entity',
                        'prefix' => 'Arobases\SyliusRightsManagementPlugin\Entity',
                        'alias' => 'ArobasesSyliusRightsManagementPlugin',
                    ],
                ],
            ],
        ]);
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }
}
