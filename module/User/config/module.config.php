<?php
/**
 * Module configuration
 */

namespace User;

use Zend\Router\Http\Segment;

return [
    // Module router configuration
    'router' => [
        'routes' => [
            'user' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/user[/]',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'auth' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'auth[/:action][:suffix]',
                            'constraints' => [
                                //'action' => '(index|login|logout|sign-up|activated|active|forgot-password|reset-password)',
                                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'auth_detail' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'auth/:action/:key[:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                                'key' => '[a-zA-Z0-9]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],

                    'profile' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'profile[/:action][:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\ProfileController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'profile_detail' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'profile/:action/:key[:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                                'key' => '[a-zA-Z0-9]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\ProfileController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                ],
            ],
        ]
    ],

    //Module controller configuration
    'controllers' => [
        'factories' => [
            Controller\AuthController::class => Controller\Factory\AuthControllerFactory::class,
            Controller\ProfileController::class => Controller\Factory\UserControllerFactory::class,
        ],
    ],


    // Module view configuration
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],


    // Module service manager configuration
    'service_manager' => [
        'factories' => [
            Service\UserManager::class => Service\Factory\UserManagerFactory::class,
            Service\AuthManager::class => Service\Factory\AuthManagerFactory::class,
            Service\AuthAdapter::class => Service\Factory\AuthAdapterFactory::class,
            Service\AuthService::class => Service\Factory\AuthServiceFactory::class,
        ],
    ],

    // Doctrine entity configuration
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

    // User mail service configuration
    'mail' => [
        'template' => require(__DIR__ . '/module.config.mail_tpl.php'),
    ],

    // User module configuration
    'user' => [
        'auth' => [
            'reset_password_expired' => 24, // Hours
        ],
    ],

    /**
     * Module access filter configuration.
     * Default limit all action access.
     * If want action can been accessed by unauthenticated user. just register bellow the configuration
     * Notice: * is a special flag for the controller's all actions can be access anonymous.
     */
    'access_filter' => [
        'controllers' => [
            Controller\AuthController::class => ['*'], // AuthController public all actions.
        ],
    ],
];
