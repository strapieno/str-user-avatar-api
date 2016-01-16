<?php
return [
    'imgman_mongodb' => [
        'Mongo\Db\Image' => [
            'database' => 'strapieno-image',
        ],
    ],
    'imgman_adapter_mongo' => [
        'ImgMan\Mongo\UserAvatar' => [
            'collection' => 'user_avatar',
            'database' => 'Mongo\Db\Image',
            'identifier' => 'identifier'
        ],
    ],
    'imgman_services' => [
        'ImgMan\Service\UserAvatar' => [
            'adapter' => 'ImgMan\Adapter\Imagick',
            'storage' => 'ImgMan\Mongo\UserAvatar',
            'helper_manager' => 'ImgMan\PluginManager',
            'renditions' => [
                'thumb' => [
                    'fitOut' => [
                        'width' => 400,
                        'height' => 400
                    ],
                    'format' => [
                        'format' => 'jpeg'
                    ],
                ],
                'normal' => [
                    'fitOut' => [
                        'width' => 30,
                        'height' => 30,
                        'allowUpsample' => true
                    ],
                    'format' => [
                        'format' => 'jpeg'
                    ]
                ]
            ]
        ]
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
            'Strapieno\Api\UserAvatar\V1\Rest\ConnectedResource' => [
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
            'collection_class' => 'Zend\Paginator\Paginator'
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
    /*
     'zf-hal' => [
        // map each class (by name) to their metadata mappings
        'metadata_map' => [
            'Strapieno\User\Model\Entity\UserEntity' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'api-rest/user',
                'route_identifier_name' => 'user_id',
                'hydrator' => 'UserApiHydrator'
            ],
        ],
    ],
    'zf-content-validation' => [
        'Strapieno\User\Api\V1\Rest\Controller' => [
          //  'input_filter' => 'Strapieno\User\Model\InputFilter\DefaultInputFilter', FIXME
          //  'POST' => 'Strapieno\User\Model\InputFilter\CreateInputFilter' FIXME
        ]
    ]
    */
];
