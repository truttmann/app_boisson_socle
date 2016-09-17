<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'RestApi\Controller\UserController' => 'RestApi\Controller\UserController',
            'RestApi\Controller\MemberController' => 'RestApi\Controller\MemberController',
            'RestApi\Controller\CategorieController' => 'RestApi\Controller\CategorieController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'user-rest' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/rest-user[/:token]',
                    'constraints' => array(
                        'token'     => '[a-zA-Z0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'RestApi\Controller\UserController',
                    ),
                ),
            ),
            'member-rest' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/rest-member[/:id]',
                    'constraints' => array(
                        'id'     => '[a-zA-Z0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'RestApi\Controller\MemberController',
                    ),
                ),
            ),
            'categorie-rest' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/rest-categorie[/:id]',
                    'constraints' => array(
                        'id'     => '[a-zA-Z0-9\_]+',
                    ),
                    'defaults' => array(
                        'controller' => 'RestApi\Controller\CategorieController',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);