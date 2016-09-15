<?php
namespace Application\Service;

use Zend\Session\Container;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use \Zend\Mail\Transport\SmtpOptions;

require_once dirname(dirname(dirname(__DIR__))).'/config/constante.inc.php';

class UserService
{
	private $sm = null;

	/**
	* Constructor
	*/
	public function __construct($sm) {
		$this->sm = $sm;
	}

	public function getLoggedUser() {
		$userContainer = new Container(NAME_SESSION_USER);
		if ( $userContainer->offsetExists('logged_user')) {
			$temp = unserialize($userContainer->logged_user);
			return $temp;
		} else {
			return null;
		}
	}

	/**
	* function to check if user is logged
	* @return bool
	*/
	public function isLoggedUser() {
		$userContainer = new Container(NAME_SESSION_USER);
		if ( $userContainer->offsetExists('logged_user')) {
			return true;
		} else {
			return false;
		}
	}

   
	/**
	 * retroune la liste des attributs d'un user
	 * @return stdClass Object
	 *
	 * */
	public function getInfosLoggedUser() {
		return $this->_infosUser;
	}

	/**
	* function qui retourne si l'utilisateur courant est super admin
	* @return bool
	*/
	public function isUserSA() {
		$return = false;
		$obj = $this->getLoggedUser();
		if(is_object($obj) && $obj->profil == "AD"){
            $return = true;
        }
		return $return;
	}

	public function setLoggedUser($login) {
        $userContainer = new Container(NAME_SESSION_USER);
		$userContainer->logged_user = serialize($login);
	}

	public function clearSession() {
		$userContainer = new Container(NAME_SESSION_USER);
		$sessionManager   = $userContainer->getManager();
		$sessionManager->destroy();
	}

	public  function hasUserAccess() {
		/* Controllers List of authorized logged off access */
    	$authorizedControllers = require(__DIR__.'/../../../config/autorized_route.php');

        $userContainer = new Container(NAME_SESSION_USER);

        /* If !logged --> Go controller login */
        $router = $this->sm->get('router');
        $request = $this->sm->get('request');
        $routeMatch = $router->match($request);
        if(is_object($routeMatch)) {
	        $controllerName = $routeMatch->getParam('controller');
            $actionName = $routeMatch->getParam('action');

            /* veriication pour les pages public */
            if (!$userContainer->offsetExists('logged_user')) {
	            if(!array_key_exists($controllerName, $authorizedControllers)) {
                    return false;
	            }
                
                if(!in_array($actionName, $authorizedControllers[$controllerName])) {
                    return false;
	            }
	        }
        } else {
        	return false;
        }
        return true;
	}

	public  function hasRight() {
		/* Controllers List of authorized logged off access */
        $aclControllers = require(__DIR__.'/../../../config/acl_route.php');
        $userContainer = new Container(NAME_SESSION_USER);

        /* If !logged --> Go controller login */
        $router = $this->sm->get('router');
        $request = $this->sm->get('request');
        $routeMatch = $router->match($request);

        if(is_object($routeMatch)) {
	        $controllerName = $routeMatch->getParam('controller');
            $actionName = $routeMatch->getParam('action');

            /* veriication pour les pages public */
            if (!$userContainer->offsetExists('logged_user')) {
                return false;
	        }
            
            /* veriication pour les pages prives */
            $temp_user = $this->getLoggedUser();
            if(!array_key_exists($temp_user->profil_id, $aclControllers)) {
                return false;
            }
            if(!array_key_exists($controllerName, $aclControllers[$temp_user->profil_id])) {
                return false;
            }
            if(!in_array($actionName, $aclControllers[$temp_user->profil_id][$controllerName])) {
                return false;
            }
        } else {
        	return false;
        }
        return true;
	}

	

	public function generatePassword() {
        $alpha = "abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ23456789";
        $numeric = "23456789";
        $special = ".-+=_,!@$#*��%&";

        $pw = str_shuffle($alpha);
        $pw = mb_substr($pw, 0 ,8);
        $t = $t2 = rand(0, 7);

        $pwn = str_shuffle($numeric);
        $pwn = mb_substr($pwn, 0 ,1);
        $pw = utf8_encode(substr_replace(utf8_decode($pw), utf8_decode($pwn), $t, 1));

        while($t2 ==  $t) {
            $t2 = rand(0, 7);
        }

        $pws = str_shuffle($special);
        $pws = mb_substr($pws, 0 ,1);
        $pw = utf8_encode(substr_replace(utf8_decode($pw), utf8_decode($pws), $t2, 1));

        return $pw;
    }
    
    public function sendMailRegenerationCle(\Application\Model\User $user, $token){
        $mail = new Message();
        $mail->setBody('Bonjour '.$user->name.' '.$user->firstname.', <br/> Voici ci-joint votre nouvelle cl&eacute; d\'activation n&eacute;cessaire pour utiliser l\'application mobile : '.$token.'.');
        $mail->setFrom('waltdistribution@lecomptoirgeneral.org', 'Walt Distribution');
        $mail->addTo($user->email, $user->name);
        $mail->setSubject('Cl&eacute; d\'activation pour votre compte');

        // Setup SMTP transport using LOGIN authentication
        $smtpOptions = new \Zend\Mail\Transport\SmtpOptions();  
        $smtpOptions->setHost('smtp.gmail.com')
                    ->setConnectionClass('login')
                    ->setName('smtp.gmail.com')
                    ->setPort(465)
                    ->setConnectionConfig(array(
                        'username' => 'tompous2604@gmail.com',
                        'password' => '26-04Mon_ange89',
                        'ssl' => 'tls',
                    ));
        
        /*$html = new \Zend\Mime\Part('<b>heii, <i>sorry</i>, i\'m going late</b>');
        $html->type = "text/html";

        $body = new \Zend\Mime\Message();
        $body->addPart($html);

        $mail->setBody($body);
        $transport->setOptions($options);
        $transport->send($mail);*/
        $transport = new \Zend\Mail\Transport\Smtp($smtpOptions);
        $transport->send($mail);
    }

}