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

class MemberController extends AbstractActionController
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
        $res = $this->getServiceLocator()->get("UserTable")->fetchAll(array("profil_id" => 2));
        
        $view->setVariable('flashMessages', $this->flashMessenger()->getMessages());
        $view->setVariable('users', $res);
        return $view;
    }
    
    public function addAction()
    {
        $form = new \Application\Form\MemberForm(array(
            "profil" => $this->getServiceLocator()->get("ProfilTable")->fetchAllActiveArray(array("id" => 2)), 
            "societe" => $this->getServiceLocator()->get("UserTable")->fetchAllSocieteActiveArray()
        ));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $obj = new User();
            $form->setInputFilter($obj->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $obj->exchangeArray($form->getData());
                
                $this->getServiceLocator()->get("UserTable")->save($obj, $this->getServiceLocator()->get('user_service')->getLoggedUser());
                
                // Redirect to list of albums
                $this->flashMessenger()->addMessage("Ajout de l'employé réussi");
                return $this->redirect()->toRoute('member');
            }
        }
        return array('form' => $form);
        //return new ViewModel();
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('member', array(
                'action' => 'add'
            ));
        }

        // Get the User with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $user = $this->getServiceLocator()->get("UserTable")->getUser($id);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('member', array(
                'action' => 'index'
            ));
        }
        
        $form  = new \Application\Form\MemberForm(array(
            "profil" => $this->getServiceLocator()->get("ProfilTable")->fetchAllActiveArray(array("id" => 2)),
            "societe" => $this->getServiceLocator()->get("UserTable")->fetchAllSocieteActiveArray()
        ));
        $form->bind($user);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                
                $this->getServiceLocator()->get("UserTable")->save($user, $this->getServiceLocator()->get('user_service')->getLoggedUser());

                // Redirect to list of albums
                $this->flashMessenger()->addMessage("Sauvegarde de l'employé réussie");
                return $this->redirect()->toRoute('member');
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
            return $this->redirect()->toRoute('member');
        }
        
        $this->getServiceLocator()->get("UserTable")->delete($id, $this->getServiceLocator()->get('user_service')->getLoggedUser());
        $this->flashMessenger()->addMessage("Suppression de l'employé réussi");
        return $this->redirect()->toRoute('member');
    }   
    
    public function regenerercleAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('member');
        }
        $user = $this->getServiceLocator()->get('UserTable')->getUser($id);
        $token = $id."_".$this->getServiceLocator()->get('user_service')->generatePassword()."_".date("YmdHis");
        
        
        $this->getServiceLocator()->get("UserTable")->regeneratecle($id, sha1($token), $this->getServiceLocator()->get('user_service')->getLoggedUser());
        
        /* Envoi du mail avec le clé d'activation */
        $this->getServiceLocator()->get('user_service')->sendMailRegenerationCle($user, $token);
        
        $this->flashMessenger()->addMessage("Régénération clé de l'employé réussie");
        return $this->redirect()->toRoute('member');
    }
}
