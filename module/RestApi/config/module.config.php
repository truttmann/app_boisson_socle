<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'RestApi\Controller\UserController' => 'RestApi\Controller\UserController',
            'RestApi\Controller\MemberController' => 'RestApi\Controller\MemberController',
            'RestApi\Controller\CategorieController' => 'RestApi\Controller\CategorieController',
            'RestApi\Controller\CommandeController' => 'RestApi\Controller\CommandeController',
            'RestApi\Controller\ProduitController' => 'RestApi\Controller\ProduitController',
            'RestApi\Controller\StockController' => 'RestApi\Controller\StockController',
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
            'commande-rest' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/rest-commande[/:id]',
                    'constraints' => array(
                        'id'     => '[a-zA-Z0-9\_]+',
                    ),
                    'defaults' => array(
                        'controller' => 'RestApi\Controller\CommandeController',
                    ),
                ),
            ),
            'produit-rest' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/rest-produit[/:id]',
                    'constraints' => array(
                        'id'     => '[a-zA-Z0-9\_]+',
                    ),
                    'defaults' => array(
                        'controller' => 'RestApi\Controller\ProduitController',
                    ),
                ),
            ),
            'stock-rest' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/rest-stock[/:id]',
                    'constraints' => array(
                        'id'     => '[a-zA-Z0-9\_]+',
                    ),
                    'defaults' => array(
                        'controller' => 'RestApi\Controller\StockController',
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