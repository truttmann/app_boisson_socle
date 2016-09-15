<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SessionManager;
use Zend\Session\Container;

use Application\Model\UserTable;
use Application\Model\HistoriqueTable;
use Application\Model\ProfilTable;
use Application\Model\ProductTable;
use Application\Model\CategoryTable;




class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        /* Init sessions */
        $this->initializeSession($e);
        
        /*  */
        $moduleRouteListener->attach($eventManager);

        
        /* Add event checkin login */
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array($this, 'authPreDispatch'),1); 
        
        $this->setLayoutGlobals($e);
    }
    
    public function initializeSession($em)
    {
        $sessionConfig = new SessionConfig();
        $sessionManager = new SessionManager($sessionConfig);
        $sessionManager->start();
        Container::setDefaultManager($sessionManager);
    }
    
    /**
     * Authenticate user or redirect to log in
     */
    public function authPreDispatch($e) {
        $application            = $e->getApplication();
        $sm                     = $application->getServiceManager();
        $router = $sm->get('router');
        $request = $sm->get('request');
        $routeMatch = $router->match($request);
        
        $controller             = $routeMatch->getParam('controller');
        if(!preg_match("/^RestApi\\\.*$/", $controller)) {
            if(!$sm->get('user_service')->hasUserAccess()){
                $e->getTarget()->plugin('redirect')->toRoute('login');
            }
            if($sm->get('user_service')->isLoggedUser() && !$sm->get('user_service')->hasRight()){
                $e->getTarget()->plugin('redirect')->toRoute('dashboard');
            }
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    // getAutoloaderConfig() and getConfig() methods here
    public function getServiceConfig()
    {
        return array(
             'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'factories' => array(
                'UserTable' =>  function($sm) {
                    $tableGateway = $sm->get('UserTableGateway');
                    $table = new UserTable($tableGateway);
                    return $table;
                },
                'UserTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\User());
                    return new \Zend\Db\TableGateway\TableGateway('user', $dbAdapter, null, $resultSetPrototype);
                },
                'ProfilTable' =>  function($sm) {
                    $tableGateway = $sm->get('ProfilTableGateway');
                    $table = new ProfilTable($tableGateway);
                    return $table;
                },
                'ProfilTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Profil());
                    return new \Zend\Db\TableGateway\TableGateway('profil', $dbAdapter, null, $resultSetPrototype);
                },
                'ProductTable' =>  function($sm) {
                    $tableGateway = $sm->get('ProductTableGateway');
                    $table = new ProductTable($tableGateway);
                    return $table;
                },
                'ProductTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Product());
                    return new \Zend\Db\TableGateway\TableGateway('produit', $dbAdapter, null, $resultSetPrototype);
                },
                'CategoryTable' =>  function($sm) {
                    $tableGateway = $sm->get('CategoryTableGateway');
                    $table = new CategoryTable($tableGateway);
                    return $table;
                },
                'CategoryTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Category());
                    return new \Zend\Db\TableGateway\TableGateway('categorie', $dbAdapter, null, $resultSetPrototype);
                },
                'HistoriqueTable' =>  function($sm) {
                    $tableGateway = $sm->get('HistoriqueTableGateway');
                    $table = new HistoriqueTable($tableGateway);
                    return $table;
                },
                'HistoriqueTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Historique());
                    return new \Zend\Db\TableGateway\TableGateway('badge_historique', $dbAdapter, null, $resultSetPrototype);
                },
                'TypeEmbouteillageTable' =>  function($sm) {
                    $tableGateway = $sm->get('TypeEmbouteillageTableGateway');
                    $table = new Model\TypeEmbouteillageTable($tableGateway);
                    return $table;
                },
                'TypeEmbouteillageTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\TypeEmbouteillage());
                    return new \Zend\Db\TableGateway\TableGateway('type_embouteillage', $dbAdapter, null, $resultSetPrototype);
                },
                'TypeColisageTable' =>  function($sm) {
                    $tableGateway = $sm->get('TypeColisageTableGateway');
                    $table = new Model\TypeColisageTable($tableGateway);
                    return $table;
                },
                'TypeColisageTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\TypeColisage());
                    return new \Zend\Db\TableGateway\TableGateway('type_colisage', $dbAdapter, null, $resultSetPrototype);
                },
                'StockTable' =>  function($sm) {
                    $tableGateway = $sm->get('StockTableGateway');
                    $table = new Model\StockTable($tableGateway);
                    return $table;
                },
                'StockTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Stock());
                    return new \Zend\Db\TableGateway\TableGateway('stock', $dbAdapter, null, $resultSetPrototype);
                },
                'CommandeTable' =>  function($sm) {
                    $tableGateway = $sm->get('CommandeTableGateway');
                    $table = new Model\CommandeTable($tableGateway);
                    return $table;
                },
                'CommandeTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Commande());
                    return new \Zend\Db\TableGateway\TableGateway('commande', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
    
    public function setLayoutGlobals($e)
    {
        $application			= $e->getApplication();
    	$sm 					= $application->getServiceManager();
        $translator 			= $sm->get('translator');
        $eventManager        	= $application->getEventManager();
        $moduleRouteListener 	= new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);


        /* SET defaults Controller, action to view */
        $router = $sm->get('router');
        $request = $sm->get('request');
        $routeMatch = $router->match($request);
        $viewModel              = $e->getViewModel();
        
        if($routeMatch){
	        $action                 = $routeMatch->getParam('action');
	    	$controller             = $routeMatch->getParam('controller');
        }else{
        	$action                 = null;
        	$controller             = null;
        }
        
        $menu_active = 'home';
        if($controller == "Application\Controller\User") {
            $menu_active = 'user';
        } elseif($controller == "Application\Controller\Member") {
            $menu_active = 'member';
        } elseif($controller == "Application\Controller\Catalogue") {
            $menu_active = 'catalogue';
        } elseif($controller == "Application\Controller\Stock") {
            $menu_active = 'stock';
        } elseif($controller == "Application\Controller\Commande") {
            $menu_active = 'commande';
        }
        $viewModel->setVariable('menu_active', $menu_active);

    	$module                 = __NAMESPACE__;
    	$siteName               = "Débit de boissons";

        $viewModel->setVariable('controller', $controller);
        $viewModel->setVariable('action', $action);

        // set the user informations
        if($sm->get('user_service')->hasUserAccess()) {
            $userTab = $sm->get('user_service')->getLoggedUser();
            $viewModel->setVariable('user_info', $userTab);
            if($sm->get('user_service')->isUserSA()) {
                $viewModel->setVariable('SA', true);
            }
        }

    	// Getting the view helper manager from the application service manager
    	$viewHelper = $sm->get('viewhelpermanager');

    	// Getting the headTitle helper from the view helper manager
    	$headTitleHelper   = $viewHelper->get('headTitle');

    	// Setting a separator string for segments
    	$headTitleHelper->setSeparator(' - ');

    	// Setting the action, controller, module and site name as title segments
        //$headTitleHelper->append($module);
        $headTitleHelper->append($siteName);
//         $headTitleHelper->append($controller);
//     	$headTitleHelper->append($action);


    	$headLink 	= $viewHelper->get('headLink');

    	/* Self base path */
        $basePath 		= URL_FRONT;
        $viewModel->setVariable('basePath', $basePath);

    	/* SET CSS */
    	/*$headLink
//     			->appendStylesheet("http://fonts.googleapis.com/css?family=Comfortaa:400,700")
		    	->appendStylesheet("$basePath/css/bootstrap.min.css")
		    	->appendStylesheet("$basePath/css/font-awesome.min.css")
		    	->appendStylesheet("$basePath/css/bootstrap.fileupload.min.css")
		    	->appendStylesheet("$basePath/css/bootstrap-checkbox.css")
                ->appendStylesheet("$basePath/css/bootstrap-select.min.css")
                ->appendStylesheet("$basePath/css/toastr.min.css")
                ->appendStylesheet("$basePath/css/datepicker3.css")
		    	->appendStylesheet("$basePath/css/bootstrapValidator.min.css")
                ->appendStylesheet("$basePath/css/bootstrap-dialog.min.css")
		    	->appendStylesheet("$basePath/css/jquery-ui.min.css")
		    	->appendStylesheet("$basePath/css/jquery-ui.theme.min.css")
                ->appendStylesheet("$basePath/css/serenis.css")
		    	;*/

    	/* SET JS */
    	$headScript	= $viewHelper->get('headScript');
    	/*$headScript
            //->appendFile("$basePath/js/html5.js", "text/javascript", ['conditional' => 'lt IE9'])
            ->prependScript( 'var BASE_URL = "' . $basePath . '";' )
            ->prependFile("$basePath/js/bootstrap.min.js", "text/javascript")
            ->prependFile("$basePath/js/jquery-ui.js", "text/javascript")
            ->prependFile("$basePath/js/jquery-1.11.2.js", "text/javascript")

            ->appendFile("$basePath/js/bootstrap.fileupload.min.js", "text/javascript")
            ->appendFile("$basePath/js/bootstrap-checkbox.js", "text/javascript")
            ->appendFile("$basePath/js/bootstrap-select.min.js", "text/javascript")
            ->appendFile("$basePath/js/toastr.min.js", "text/javascript")
            ->appendFile("$basePath/js/equalheights.js", "text/javascript")
            ->appendFile("$basePath/js/bootstrapValidator.min.js", "text/javascript")
            ->appendFile("$basePath/js/language/fr_FR.js", "text/javascript")
            ->appendFile("$basePath/js/bootstrap-datepicker.js", "text/javascript")
            ->appendFile("$basePath/js/locales/bootstrap-datepicker.fr.js", "text/javascript")
            ->appendFile("$basePath/js/bootstrap-dialog.min.js", "text/javascript")
            ->appendFile("$basePath/js/bootbox.js", "text/javascript")
            ->appendFile("$basePath/js/spinner.js", "text/javascript")

            ->offsetSetFile(9999, "$basePath/js/actions.js")
		    	;
*/
    	/* Favicon */
    	$headLink->headLink([ 'rel' => 'shortcut icon', 'type' => 'image/vnd.microsoft.icon', 'href' =>"$basePath/img/favicon.ico"]);

    	/* SET Generals METAS */
    	$metas = $viewHelper->get('headMeta');
    	$metas
		    	->appendHttpEquiv('Content-Language', 'fr-FR')
		    	->setCharset('UTF-8');
		    	;

    	$metas
    			->appendName('viewport', 'width=device-width, initial-scale=1.0')
		    	->appendName('keywords', 'Kwrds')
		    	->appendName('description', 'Apreva DESC')
		    	;
    }
}
