<?php

declare(strict_types=1);
namespace Linderp\SuluMailingListBundle\DependencyInjection;

use Linderp\SuluMailingListBundle\Entity\Newsletter\Newsletter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SuluMailingListExtension extends Extension implements PrependExtensionInterface
{

    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $config = $this->processConfiguration(new Configuration(), $configs);
        $container->setParameter(
            'sulu_mailing_list.mjml.app_id',
            $config['mjml']['app_id']
        );
        $container->setParameter(
            'sulu_mailing_list.mjml.secret_key',
            $config['mjml']['secret_key']
        );
        $container->setParameter(
            'sulu_mailing_list.mjml.caching',
            $config['mjml']['caching']
        );
        $container->setParameter(
            'sulu_mailing_list.no_reply_email',
            $config['no_reply_email']
        );
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('sulu_admin')) {
            $container->prependExtensionConfig(
                'sulu_admin',
                [
                    'lists' => [
                        'directories' => [
                            __DIR__ . '/../Resources/config/lists',
                        ],
                    ],
                    'forms' => [
                        'directories' => [
                            __DIR__ . '/../Resources/config/forms'
                        ],
                    ],
                    'resources' => [
                        'filtered_contacts' => [
                            'routes' => [
                                'list' => 'app.get_filtered_contacts'
                            ]
                        ],
                        'newsletters' => [
                            'routes' => [
                                'list' => 'app.get_newsletter_list',
                                'detail' => 'app.get_newsletter'
                            ]
                        ],
                        'newsletters_subscriptions' => [
                            'routes' => [
                                'list' => 'app.get_newsletter_subscriptions_list',
                                'detail' => 'app.get_newsletter_subscription'
                            ]
                        ],
                        'newsletters_mails' => [
                            'routes' => [
                                'list' => 'app.get_newsletter_mail_list',
                                'detail' => 'app.get_newsletter_mail'
                            ]
                        ],
                    ],
                    'field_type_options' => [
                        'selection' => [
                            'newsletter_selection' => [
                                'default_type' => 'list_overlay',
                                'resource_key' => Newsletter::RESOURCE_KEY,
                                'types' => [
                                    'list_overlay' => [
                                        'adapter' => 'table',
                                        'list_key' => 'newsletters',
                                        'display_properties' => ['title'],
                                        'icon' => 'fa-newspaper',
                                        'label' => 'mailingList.label',
                                        'overlay_title' => 'mailingList.label',
                                    ],
                                    'auto_complete' => [
                                        'display_property' => 'title',
                                        'search_properties' => ['title'],
                                    ]
                                ],
                            ],
                            'filtered_contact_selection' => [
                                'default_type' => 'list_overlay',
                                'resource_key' => 'filtered_contacts',
                                'types' => [
                                    'list_overlay' => [
                                        'adapter' => 'table',
                                        'list_key' => 'filtered_contacts',
                                        'display_properties' => ['firstName', 'lastName', 'email'],
                                        'icon' => 'su-user',
                                        'label' => 'mailingListMail.props.contacts',
                                        'overlay_title' => 'mailingListMail.props.contacts',
                                    ],
                                    'auto_complete' => [
                                        'display_property' => 'firstName',
                                        'search_properties' => ['firstName'],
                                    ]
                                ],
                            ],
                        ],
                    ],
                ]
            );
        }
    }
}
