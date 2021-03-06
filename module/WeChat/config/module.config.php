<?php
/**
 * module.config.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace WeChat;


return [

    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                ],
            ],
        ],
    ],

    'service_manager' => [
        'factories' => [
            Service\AccountService::class => Service\Factory\BaseEntityFactory::class,
            Service\TagService::class => Service\Factory\BaseEntityFactory::class,
            Service\ClientService::class => Service\Factory\BaseEntityFactory::class,
            Service\QrCodeService::class => Service\Factory\BaseEntityFactory::class,
            Service\MenuService::class => Service\Factory\BaseEntityFactory::class,
            Service\OrderService::class => Service\Factory\BaseEntityFactory::class,
            Service\InvoiceService::class => Service\Factory\BaseEntityFactory::class,
            Service\OauthService::class => Service\Factory\BaseEntityFactory::class,
            Service\WeChatService::class => Service\Factory\WeChatFactory::class,
        ],
    ],
];