<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

return array(
    'router' => array(
        'routes' => array(
            'login' => array( 'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/login',
                    'defaults' => array(
                        'controller' => 'Application\Controller\User',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'check' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/check',
                            'defaults' => array(
                                'action'     => 'checklogin',
                            ),
                        ),
                    ),
                    'disconnect' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/disconnect',
                            'defaults' => array(
                                'action'     => 'logout',
                            ),
                        ),
                    ),
                ),
            ),
            'dashboard' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'user' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/user',
                    'defaults' => array(
                        'controller' => 'Application\Controller\User',
                        'action'     => 'list',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'ajouter' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/add',
                            'defaults' => array(
                                'action'     => 'add',
                            ),
                        ),
                    ),
                    'editier' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/edit/:id',
                            'constraints' => array(
                                'id' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'edit',
                            ),
                        ),
                    ),
                    'supprimer' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/delete/:id',
                            'constraints' => array(
                                'id' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'delete',
                            ),
                        ),
                    ),
                    'regenerercle' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/regenerercle/:id',
                            'constraints' => array(
                                'id' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'regenerercle',
                            ),
                        ),
                    ),
                ),
            ),
            'member' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/member',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Member',
                        'action'     => 'list',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'ajouter' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/add',
                            'defaults' => array(
                                'action'     => 'add',
                            ),
                        ),
                    ),
                    'editier' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/edit/:id',
                            'constraints' => array(
                                'id' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'edit',
                            ),
                        ),
                    ),
                    'supprimer' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/delete/:id',
                            'constraints' => array(
                                'id' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'delete',
                            ),
                        ),
                    ),
                    'regenerercle' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/regenerercle/:id',
                            'constraints' => array(
                                'id' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'regenerercle',
                            ),
                        ),
                    ),
                ),
            ),
            'catalogue' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/catalogue',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Catalogue',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'listproduit' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/listProduct/:idCategorie',
                            'constraints' => array(
                                'idCategorie' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'listProduct',
                            ),
                        ),
                    ),
                    'ajoutercategorie' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/addCategory',
                            'defaults' => array(
                                'action'     => 'addCategory',
                            ),
                        ),
                    ),
                    'ajouterproduit' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/addProduct/:idCategorie',
                            'constraints' => array(
                                'idCategorie' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'addProduct',
                            ),
                        ),
                    ),
                    'editcategorie' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/editCategory/:idCategorie',
                            'constraints' => array(
                                'idCategorie' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'editCategory',
                            ),
                        ),
                    ),
                    'editproduit' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/editProduct/:idCategorie/:idProduct',
                            'constraints' => array(
                                'idCategorie' => '[0-9]',
                                 'idProduct' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'editProduct',
                            ),
                        ),
                    ),
                    'delcategorie' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/delCategory/:idCategorie',
                            'constraints' => array(
                                'idCategorie' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'delCategory',
                            ),
                        ),
                    ),
                    'delproduit' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/delProduct/:idCategorie/:idProduct',
                            'constraints' => array(
                                'idCategorie' => '[0-9]',
                                'idProduct' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'delProduct',
                            ),
                        ),
                    ),
                ),
            ),
            'stock' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/stock',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Stock',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'stockclient' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/detail/:idUser',
                            'constraints' => array(
                                'idUser' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'detailStock',
                            ),
                        ),
                    ),
                    'ajoutproduct' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:idUser/addProduct',
                            'constraints' => array(
                                'idUser' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'addProduct',
                            ),
                        ),
                    ),
                    'delproduct' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:idUser/delProduct/:idStock',
                            'constraints' => array(
                                'idUser' => '[0-9]',
                                'idStock' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'delProduct',
                            ),
                        ),
                    ),
                    'editproduct' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:idUser/editProduct/:idStock',
                            'constraints' => array(
                                'idUser' => '[0-9]',
                                'idStock' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'editProduct',
                            ),
                        ),
                    ),
                ),
            ),
            'commande' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/commande',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Commande',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'listcommande' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:idUser/list',
                            'constraints' => array(
                                'idUser' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'listCommande',
                            ),
                        ),
                    ),
                    
                    'detailcommande' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:idUser/detail/:idCommande',
                            'constraints' => array(
                                'idUser' => '[0-9]',
                                'idCommande' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'editCommande',
                            ),
                        ),
                    ),
                    'ajoutproduct' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:idUser/addCommande',
                            'constraints' => array(
                                'idUser' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'addCommande',
                            ),
                        ),
                    ),
                    'delproduct' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:idUser/delCommande/:idCommande',
                            'constraints' => array(
                                'idUser' => '[0-9]',
                                'idCommande' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'delCommande',
                            ),
                        ),
                    ),
                    'productinfo' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:idUser/info/:idProduct',
                            'constraints' => array(
                                'idUser' => '[0-9]',
                                'idProduct' => '[0-9]',
                            ),
                            'defaults' => array(
                                'action'     => 'infoProduct',
                            ),
                        ),
                    ),
                ),
            ),
            'historique' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/historique',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Historique',
                        'action'     => 'list',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            'user_service' => function($sm) { 
				return new Service\UserService($sm); 
			}
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => Controller\IndexController::class,
            'Application\Controller\User' => Controller\UserController::class,
            'Application\Controller\Member' => Controller\MemberController::class,
            'Application\Controller\Historique' => Controller\HistoriqueController::class,
            'Application\Controller\Catalogue' => Controller\CatalogueController::class,
            'Application\Controller\Stock' => Controller\StockController::class,
            'Application\Controller\Commande' => Controller\CommandeController::class,
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
