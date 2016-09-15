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
use Application\Model\Stock;

class StockController extends AbstractActionController
{
    public function indexAction()
    {
        $view = new ViewModel();
        $view->setVariable('flashMessages', $this->flashMessenger()->getMessages());
        $view->setVariable('client', $this->getServiceLocator()->get('UserTable')->fetchAllSocieteArray(null, true));
        return $view;
    }
    
    public function detailStockAction() {
        $view = new ViewModel();
        
        /* recherche du stock pour l'utilisateur fourni*/
        $id = (int) $this->params()->fromRoute('idUser', 0);
        if (!$id) {
            return $this->redirect()->toRoute('stock', array(
                'action' => 'index'
            ));
        }

        try {
            $user = $this->getServiceLocator()->get("UserTable")->getUser($id);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('user', array(
                'action' => 'index'
            ));
        }
        
        
        $r = array();
        $l = $this->getServiceLocator()->get('StockTable')->fetchAll(array('user_id' => $id));
        
        foreach ($l as $i) {
            $i->product = $this->getServiceLocator()->get('ProductTable')->getProduct($i->produit_id);
            $r[] = $i;
        }
        $view->setVariable('flashMessages', $this->flashMessenger()->getMessages());
        $view->setVariable("list_stock", $r);
        $view->setVariable("user_ref", $user);
        return $view;
    }
    
    public function addProductAction() {
        /* recherche du stock pour l'utilisateur fourni*/
        $id = (int) $this->params()->fromRoute('idUser', 0);
        if (!$id) {
            return $this->redirect()->toRoute('stock', array(
                'action' => 'index'
            ));
        }

        try {
            $user = $this->getServiceLocator()->get("UserTable")->getUser($id);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('user', array(
                'action' => 'index'
            ));
        }
        
        $form = new \Application\Form\StockForm(array("product" => $this->getServiceLocator()->get("ProductTable")->fetchAllActiveArray(array(), array("libelle ASC"))));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $obj = new Stock();
            $form->setInputFilter($obj->getInputFilter());
            
            $form->setData($request->getPost());
                
            if ($form->isValid()) {
                $obj->exchangeArray($form->getData());
                
                /* rajout du lien vers la societe */
                $obj->user_id = $user->id;
                
                $this->getServiceLocator()->get("StockTable")->save($obj, $this->getServiceLocator()->get('user_service')->getLoggedUser());
                
                // Redirect to list of albums
                $this->flashMessenger()->addMessage("Mise à jour du stock réussie");
                return $this->redirect()->toRoute('stock/stockclient', array("idUser" => $user->id));
            }
        }
        return array('form' => $form, "user_ref" => $user);
    }
    
    public function delProductAction() {
        /* recherche du stock pour l'utilisateur fourni*/
        $id = (int) $this->params()->fromRoute('idUser', 0);
        if (!$id) {
            return $this->redirect()->toRoute('stock', array(
                'action' => 'index'
            ));
        }

        try {
            $user = $this->getServiceLocator()->get("UserTable")->getUser($id);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('user', array(
                'action' => 'index'
            ));
        }
        
        /* recherche du produit */
        $idStock = (int) $this->params()->fromRoute('idStock', 0);
        if (!$idStock) {
            return $this->redirect()->toRoute('stock', array(
                'action' => 'index'
            ));
        }

        $this->getServiceLocator()->get('StockTable')->delete($idStock, $this->getServiceLocator()->get('user_service')->getLoggedUser());
        $this->flashMessenger()->addMessage("Mise à jour du stock réussie");
        return $this->redirect()->toRoute('stock/stockclient', array("idUser" => $user->id));
    }
    
    
    public function editProductAction() {
        /* recherche du stock pour l'utilisateur fourni*/
        $id = (int) $this->params()->fromRoute('idUser', 0);
        if (!$id) {
            return $this->redirect()->toRoute('stock', array(
                'action' => 'index'
            ));
        }

        try {
            $user = $this->getServiceLocator()->get("UserTable")->getUser($id);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('stock', array(
                'action' => 'index'
            ));
        }
        
        /* recherche du produit */
        $idStock = (int) $this->params()->fromRoute('idStock', 0);
        if (!$idStock) {
            return $this->redirect()->toRoute('stock', array(
                'action' => 'index'
            ));
        }
        
        $form = new \Application\Form\StockForm(array("product" => $this->getServiceLocator()->get("ProductTable")->fetchAllActiveArray(array(), array("libelle ASC"))));

        /* recherche du stock */
        $stock = $this->getServiceLocator()->get('StockTable')->getStock($idStock);
        
        $form->bind($stock);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($stock->getInputFilter());
            
            $form->setData($request->getPost());
                
            if ($form->isValid()) {
                /* rajout du lien vers la societe */
                $stock->user_id = $user->id;
                
                $this->getServiceLocator()->get("StockTable")->save($stock, $this->getServiceLocator()->get('user_service')->getLoggedUser());
                
                // Redirect to list of albums
                $this->flashMessenger()->addMessage("Mise à jour du stock réussie");
                return $this->redirect()->toRoute('stock/stockclient', array("idUser" => $user->id));
            }
        }

        return array('form' => $form, "user_ref" => $user, "stock_ref" => $stock);
    }
}
