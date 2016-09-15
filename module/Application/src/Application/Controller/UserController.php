<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\User;

class UserController extends AbstractActionController
{
    public function indexAction()
    {
        $view = new ViewModel();
        $view->setVariable('flashMessages', $this->flashMessenger()->getMessages());
        return $view;
    }
    
    public function listAction()
    {
        /* recuperation de la liste des users */
        $view = new ViewModel();
        $res = $this->getServiceLocator()->get("UserTable")->fetchAll(array("profil_id" => 1));
        
        $view->setVariable('flashMessages', $this->flashMessenger()->getMessages());
        $view->setVariable('users', $res);
        return $view;
    }
    
    public function addAction()
    {
        $form = new \Application\Form\UserForm(array("profil" => $this->getServiceLocator()->get("ProfilTable")->fetchAllActiveArray(array("id" => 1))));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $obj = new User();
            $form->setInputFilter($obj->getInputFilter());
            
            $form->setData($request->getPost());
                
            /* Check if this is not another user with this societe */
            $res = $this->getServiceLocator()->get("UserTable")->fetchAllSocieteActiveArray($request->getPost('societe'));
            $err = false;
            if(is_array($res) && count($res) != 0){
                $filter = $form->getInputFilter();
                $form->get('societe')->setMessages(array('Erreur: ce nom de société est déjà utilisé'));
                $form->setMessages(array('Erreur: ce nom de société est déjà utilisé'));
                $err = true;
            }
            if ($form->isValid() && !$err) {
                $obj->exchangeArray($form->getData());
                
                $this->getServiceLocator()->get("UserTable")->save($obj, $this->getServiceLocator()->get('user_service')->getLoggedUser());
                
                // Redirect to list of albums
                $this->flashMessenger()->addMessage("Ajout de l'utilisateur réussi");
                return $this->redirect()->toRoute('user');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('user', array(
                'action' => 'add'
            ));
        }

        // Get the User with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $user = $this->getServiceLocator()->get("UserTable")->getUser($id);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('user', array(
                'action' => 'index'
            ));
        }
        
        $form  = new \Application\Form\UserForm(array("profil" => $this->getServiceLocator()->get("ProfilTable")->fetchAllActiveArray(array("id" => 1))));
        $form->bind($user);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {                
               $this->getServiceLocator()->get("UserTable")->save($user, $this->getServiceLocator()->get('user_service')->getLoggedUser());

                // Redirect to list of albums
                $this->flashMessenger()->addMessage("Sauvegarde de l'utilisateur réussie");
                return $this->redirect()->toRoute('user');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('user');
        }
        
        $this->getServiceLocator()->get("UserTable")->delete($id, $this->getServiceLocator()->get('user_service')->getLoggedUser());
        $this->flashMessenger()->addMessage("Suppression de l'utilisateur réussi");
        return $this->redirect()->toRoute('user');
    }   
    
    public function regenerercleAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('user');
        }
        $user = $this->getServiceLocator()->get('UserTable')->getUser($id);
        $token = $id."_".$this->getServiceLocator()->get('user_service')->generatePassword()."_".date("YmdHis");
        
        
        $this->getServiceLocator()->get("UserTable")->regeneratecle($id, sha1($token), $this->getServiceLocator()->get('user_service')->getLoggedUser());
        
        /* Envoi du mail avec le clé d'activation */
        $this->getServiceLocator()->get('user_service')->sendMailRegenerationCle($user, $token);
        
        $this->flashMessenger()->addMessage("Régénération clé de l'utilisateur réussie");
        return $this->redirect()->toRoute('user');
    }
    
    public function checkloginAction(){
        $request = $this->getRequest();
        try{
            if ($request->isPost()) {
                $data = $request->getPost();

                if(!array_key_exists("login", $data)
                    || !array_key_exists("password", $data)
                    || empty($data['login'])
                    || empty($data['password'])){
                    throw new \Exception("Données non valides.");
                }
                
                $temp = $this->getServiceLocator()->get("UserTable")->getByLogin($data['login']);

                if($temp->password != md5($data['password'])) {
                    throw new \Exception("Invalid login / password.");
                }
                
                $this->getServiceLocator()->get('user_service')->setLoggedUser($temp);
                
                $this->redirect()->toRoute('dashboard');
            }
        }  catch (\Exception $e) {
            $this->flashMessenger()->addMessage($e->getMessage());
            $this->redirect()->toRoute('login');
        }
    }
    
    public function logoutAction() {
        $this->getServiceLocator()->get('user_service')->clearSession();
        $this->redirect()->toRoute('login');
    }
}
