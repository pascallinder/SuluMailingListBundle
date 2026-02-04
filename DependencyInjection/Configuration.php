<?php

declare(strict_types=1);

namespace Linderp\SuluMailingListBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sulu_mailing_list');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('mjml')
                    ->isRequired()
                    ->children()
                        ->scalarNode('app_id')

                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('secret_key')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('caching')
                            ->defaultTrue()
                        ->end()
                ->end()
            ->end()
            ->scalarNode('no_reply_email')
                ->isRequired()
                ->cannotBeEmpty()
            ->end();

        return $treeBuilder;
    }
}
