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

class CommandeController extends AbstractActionController
{
    public function indexAction()
    {
        $view = new ViewModel();
        $view->setVariable('flashMessages', $this->flashMessenger()->getMessages());
        $view->setVariable('client', $this->getServiceLocator()->get('UserTable')->fetchAllSocieteArray(null, true));
        return $view;
    }
    
    public function listCommandeAction() {
        $view = new ViewModel();
        
        /* recherche du stock pour l'utilisateur fourni*/
        $id = (int) $this->params()->fromRoute('idUser', 0);
        if (!$id) {
            return $this->redirect()->toRoute('commande', array(
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
        $l = $this->getServiceLocator()->get('CommandeTable')->fetchAll(array('created_for' => $id));
        
        $view->setVariable('flashMessages', $this->flashMessenger()->getMessages());
        $view->setVariable("list_comande", $l);
        $view->setVariable("user_ref", $user);
        return $view;
    }
    
    public function addCommandeAction() {
        /* recherche du stock pour l'utilisateur fourni*/
        $id = (int) $this->params()->fromRoute('idUser', 0);
        if (!$id) {
            return $this->redirect()->toRoute('commande', array(
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
        
        $products = $this->getServiceLocator()->get("ProductTable")->fetchAllActiveArray(array(), array("libelle ASC"));
        $products_id = array_keys($products);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $p_c = array();
            foreach( $data as $k => $i){
                if(preg_match("/prod\_[0-9]\_.*/", $k)) {
                    $temp = explode("_", $k);
                    if(count($temp) != 4) {
                        continue;
                    }
                    if(! is_numeric($temp[1])) {
                        continue;
                    }
                    
                    /* recherche de l'existance du produit */
                    $t2 = $this->getServiceLocator()->get('ProductTable')->getProduct($temp[1]);
                    if(! is_object($t2)) {
                        continue;
                    }
                    
                    if(array_key_exists($t2->id, $p_c)){
                        $p_c[$t2->id]['value'] = $p_c[$t2->id]['value'] + $i; 
                    } else {
                        $p_c[$t2->id] = array("product" => $t2, "value" => $i);
                    }
                }
            }
            
            /* Creation de la commande */
            $com = new \Application\Model\Commande();
            $com->created_for = $id;
            $com->created_by = $this->getServiceLocator()->get('user_service')->getLoggedUser()->id;
            $com->status = 1;
                
            /* recuperation du numéro unique */
            $ok = false;
            while($ok == false){
                $num = rand(0, 100000);
                $g = $this->getServiceLocator()->get('CommandeTable')->isNumeroExist($num);
                if($g == false) {
                    $ok = true;
                    $com->numero = $num;
                }
            }
            
            $com->id = $this->getServiceLocator()->get('CommandeTable')->save($com, $this->getServiceLocator()->get('user_service')->getLoggedUser());
            
            /* Rattachement de l'ensemble des produits */
            $m_ht = 0;
            $m_taxe1 = 0;
            $m_taxe2 = 0;
            $m_tva = 0;
            $m_ttx = 0;

            foreach($p_c as $item) {
                $this->getServiceLocator()->get('CommandeTable')->addProduct($com, $item["product"], $item["value"], $this->getServiceLocator()->get('user_service')->getLoggedUser());
            
                /* on va calculer les montants */
                $m_ht += ($item["value"] * $item["product"]->prix_base);
                $m_taxe1 += ($item["value"] * $item["product"]->montant_taxe1);
                $m_taxe2 += ($item["value"] * $item["product"]->montant_taxe2);
                $m_tva += ($item["value"] * $item["product"]->montant_tva);
                $m_ttx += ($item["value"] * $item["product"]->montant_total);
            }
          
            /* Mise à jour de la commande avec les montants */
            $com->montant_ht = $m_ht;
            $com->montant_taxe1 = $m_taxe1;
            $com->montant_taxe2 = $m_taxe2;
            $com->montant_tva = $m_tva;
            $com->montant_ttc = $m_ttx;
            
            $this->getServiceLocator()->get('CommandeTable')->save($com, $this->getServiceLocator()->get('user_service')->getLoggedUser());
            
            /* redirection vers la page de modification */
            $this->flashMessenger()->addMessage("Mise à jour de la commande réussie");
            return $this->redirect()->toRoute('commande/listcommande', array("idUser" => $user->id)); 
        }
        return array('list_product' => $products, "user_ref" => $user);
    }
    
    public function infoProductAction(){
        $return = array("data" => null, "message" => "", "status" => "0");
        try {
            /* recherche du stock pour l'utilisateur fourni*/
            $id = (int) $this->params()->fromRoute('idUser', 0);
            if (!$id) {
                throw new \Exception("Invalid parameter idUser");
            }
            $user = $this->getServiceLocator()->get("UserTable")->getUser($id);
            if(! is_object($user)) {
                throw new \Exception("Unable to find current société");
            }
            
            /* recherche du stock pour l'utilisateur fourni*/
            $idProduct = (int) $this->params()->fromRoute('idProduct', 0);
            if (!$id) {
                throw new \Exception("Invalid parameter idProduct");
            }

            $p = $this->getServiceLocator()->get("ProductTable")->getProduct($idProduct);
            if(! is_object($p)) {
                throw new \Exception("Unable to find current société");
            }
            
            $return['status'] = 1;
            $return['data'] = $p;
        } catch (\Exception $ex) {
            $return['message'] = $ex->getMessage();
        }
        
        echo json_encode($return);
        exit;
    }
    
    public function delCommandeAction() {
        /* recherche du stock pour l'utilisateur fourni*/
        $id = (int) $this->params()->fromRoute('idUser', 0);
        if (!$id) {
            return $this->redirect()->toRoute('commande', array(
                'action' => 'index'
            ));
        }

        try {
            $user = $this->getServiceLocator()->get("UserTable")->getUser($id);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('commande', array(
                'action' => 'index'
            ));
        }
        
        /* recherche de la commande pour l'utilisateur fourni*/
        $idC = (int) $this->params()->fromRoute('idCommande', 0);
        if (!$idC) {
            return $this->redirect()->toRoute('commande', array(
                'action' => 'index'
            ));
        }

        try {
            $com = $this->getServiceLocator()->get("CommandeTable")->getCommande($idC);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('commande', array(
                'action' => 'index'
            ));
        }
        
        /*if($com->status != 1) {
            $this->flashMessenger()->addMessage("Impossible de supprimer une commande qui n'est pas en cours");
            return $this->redirect()->toRoute('commande/listcommande', array("idUser" => $user->id));
        }*/
        
        $this->getServiceLocator()->get("CommandeTable")->delete($idC, $this->getServiceLocator()->get('user_service')->getLoggedUser());
        $this->flashMessenger()->addMessage("Mise à jour de la commande réussie");
        return $this->redirect()->toRoute('commande/listcommande', array("idUser" => $user->id));
    }
    
    public function editCommandeAction() {
        $view = new ViewModel();
        
        /* recherche du stock pour l'utilisateur fourni*/
        $id = (int) $this->params()->fromRoute('idUser', 0);
        if (!$id) {
            return $this->redirect()->toRoute('commande', array(
                'action' => 'index'
            ));
        }

        try {
            $user = $this->getServiceLocator()->get("UserTable")->getUser($id);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('commande', array(
                'action' => 'index'
            ));
        }
        
        /* recherche de la commande pour l'utilisateur fourni*/
        $idC = (int) $this->params()->fromRoute('idCommande', 0);
        if (!$idC) {
            return $this->redirect()->toRoute('commande', array(
                'action' => 'index'
            ));
        }

        try {
            $com = $this->getServiceLocator()->get("CommandeTable")->getCommande($idC);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('commande', array(
                'action' => 'index'
            ));
        }
        
        /* si la commande n'est pas en cours, nous ne pouvons la modifier */
        /*if($com->status != 1) {
            $this->flashMessenger()->addMessage("Impossible de modifier une commande qui n'est pas en cours");
            return $this->redirect()->toRoute('commande/listcommande', array("idUser" => $user->id));
        }*/
        
        /* recupération de la liste des produits et caractéristiques */
        $view->setVariable('commande', $com);
        $view->setVariable('list_product_commande', $this->getServiceLocator()->get("CommandeTable")->getCommandeDetail($idC));
        
        $products = $this->getServiceLocator()->get("ProductTable")->fetchAllActiveArray(array(), array("libelle ASC"));
        $view->setVariable('list_product', $products);
        $view->setVariable('user_ref', $user);
        
        $products_id = array_keys($products);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $p_c = array();
            foreach( $data as $k => $i){
                if(preg_match("/prod\_[0-9]\_.*/", $k)) {
                    $temp = explode("_", $k);
                    if(count($temp) != 4) {
                        continue;
                    }
                    if(! is_numeric($temp[1])) {
                        continue;
                    }
                    
                    /* recherche de l'existance du produit */
                    $t2 = $this->getServiceLocator()->get('ProductTable')->getProduct($temp[1]);
                    if(! is_object($t2)) {
                        continue;
                    }
                    
                    if(array_key_exists($t2->id, $p_c)){
                        $p_c[$t2->id]['value'] = $p_c[$t2->id]['value'] + $i; 
                    } else {
                        $p_c[$t2->id] = array("product" => $t2, "value" => $i);
                    }
                }
            }
            
            /* suppression de tous les anciens produits */
            $this->getServiceLocator()->get('CommandeTable')->removeAllProduct($com, $this->getServiceLocator()->get('user_service')->getLoggedUser());
            
            /* Rattachement de l'ensemble des produits */
            $m_ht = 0;
            $m_taxe1 = 0;
            $m_taxe2 = 0;
            $m_tva = 0;
            $m_ttx = 0;
            foreach($p_c as $item) {
                $this->getServiceLocator()->get('CommandeTable')->addProduct($com, $item["product"], $item["value"], $this->getServiceLocator()->get('user_service')->getLoggedUser());
            
                /* on va calculer les montants */
                $m_ht += ($item["value"] * $item["product"]->prix_base);
                $m_taxe1 += ($item["value"] * $item["product"]->montant_taxe1);
                $m_taxe2 += ($item["value"] * $item["product"]->montant_taxe2);
                $m_tva += ($item["value"] * $item["product"]->montant_tva);
                $m_ttx += ($item["value"] * $item["product"]->montant_total);
            }
          
            /* Mise à jour de la commande avec les montants */
            $com->montant_ht = $m_ht;
            $com->montant_taxe1 = $m_taxe1;
            $com->montant_taxe2 = $m_taxe2;
            $com->montant_tva = $m_tva;
            $com->montant_ttc = $m_ttx;
            $com->status = ((array_key_exists('status', $data))?$data['status']:-1);
            
            $this->getServiceLocator()->get('CommandeTable')->save($com, $this->getServiceLocator()->get('user_service')->getLoggedUser());
            
            /* redirection vers la page de modification */
            $this->flashMessenger()->addMessage("Mise à jour de la commande réussie");
            return $this->redirect()->toRoute('commande/listcommande', array("idUser" => $user->id)); 
        }
        
        $view->setVariable('flashMessages', $this->flashMessenger()->getMessages());
        return $view;
    }
}
