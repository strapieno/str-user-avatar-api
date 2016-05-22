<?php
return [
    'service_manager' => [
        'factories' => [
            'Strapieno\Utils\Listener\ListenerManager' => 'Strapieno\Utils\Listener\ListenerManagerFactory'
        ],
        'invokables' => [
            'Strapieno\Utils\Delegator\AttachListenerDelegator' =>  'Strapieno\Utils\Delegator\AttachListenerDelegator'
        ],
        'aliases' => [
            'listenerManager' => 'Strapieno\Utils\Listener\ListenerManager'
        ]
    ],
    'user-not-found' => [
        'api-rest/user/avatar'
    ],
    // Register listener to listener manager
    'service-listeners' => [
        'initializers' => [
            'Strapieno\User\Model\UserModelInizializer'
        ],
        'invokables' => [
            'Strapieno\UserAvatar\Api\Listener\UserRestListener'
                => 'Strapieno\UserAvatar\Api\Listener\UserRestListener'
        ]
    ],
    'attach-listeners' => [
        'Strapieno\UserAvatar\Api\V1\Rest\Controller' => [
            'Strapieno\UserAvatar\Api\Listener\UserRestListener'
        ]
    ],
    'controllers' => [
        'delegators' => [
            'Strapieno\UserAvatar\Api\V1\Rest\Controller' => [
                'Strapieno\Utils\Delegator\AttachListenerDelegator',
            ]
        ],
    ],
    'router' => [
        'routes' => [
            'api-rest' => [
                'child_routes' => [
                    'user' => [
                        'child_routes' => [
                            'avatar' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/avatar',
                                    'defaults' => [
                                        'controller' => 'Strapieno\UserAvatar\Api\V1\Rest\Controller'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    'imgman-apigility' => [
        'imgman-connected' => [
            'Strapieno\UserAvatar\Api\V1\Rest\ConnectedResource' => [
                'service' => 'ImgMan\Service\UserAvatar'
            ],
        ],
    ],
    'zf-rest' => [
        'Strapieno\UserAvatar\Api\V1\Rest\Controller' => [
            'service_name' => 'user-avatar',
            'listener' => 'Strapieno\UserAvatar\Api\V1\Rest\ConnectedResource',
            'route_name' => 'api-rest/user/avatar',
            'route_identifier_name' => 'user_id',
            'entity_http_methods' => [
                0 => 'GET',
                2 => 'PUT',
                3 => 'DELETE'
            ],
            'page_size' => 10,
            'page_size_param' => 'page_size',
            'collection_class' => 'Zend\Paginator\Paginator',
            'entity_class' => 'Strapieno\UserAvatar\Model\Entity\UserAvatarEntity',
        ]
    ],
    'zf-content-negotiation' => [
        'accept_whitelist' => [
            'Strapieno\UserAvatar\Api\V1\Rest\Controller' => [
                'application/hal+json',
                'application/json'
            ],
        ],
        'content_type_whitelist' => [
            'Strapieno\UserAvatar\Api\V1\Rest\Controller' => [
                'application/json',
                'multipart/form-data',
            ],
        ],
    ],
     'zf-hal' => [
        // map each class (by name) to their metadata mappings
       'metadata_map' => [
            'Strapieno\UserAvatar\Model\Entity\UserAvatarEntity' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'api-rest/user/avatar',
                'route_identifier_name' => 'user_id',
                'hydrator' => 'UserAvatarApiHydrator',
            ],
        ],
    ],
    'zf-content-validation' => [
        'Strapieno\UserAvatar\Api\V1\Rest\Controller' => [
            'input_filter' => 'UserAvatarInputFilter',
        ],
    ],
    'strapieno_input_filter_specs' => [
        'UserAvatarInputFilter' => [
            [
                'name' => 'blob',
                'required' => true,
                'allow_empty' => false,
                'continue_if_empty' => false,
                'validators' => [
                    0 => [
                        'name' => 'fileuploadfile',
                        'break_chain_on_failure' => true,
                    ],
                    1 => [
                        'name' => 'filesize',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'min' => '20KB',
                            'max' => '8MB',
                        ],
                    ],
                    2 => [
                        'name' => 'filemimetype',
                        'options' => [
                            'mimeType' => [
                                'image/png',
                                'image/jpeg',
                            ],
                            'magicFile' => false,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
