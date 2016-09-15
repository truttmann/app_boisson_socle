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
use Application\Model\Category;

class CatalogueController extends AbstractActionController
{
    public function indexAction()
    {
        $view = new ViewModel();
        
        /* liste de toutes les catégories disponibles */
        $r = array();
        $s = array();
        $temp = $this->getServiceLocator()->get('CategoryTable')->fetchAll(array(), "libelle ASC");
        foreach ($temp as $t ) {
            if($t->categorie_id != null) {
                if(! array_key_exists($t->categorie_id, $s)) {
                    $s[$t->categorie_id] = array();
                }
                $s[$t->categorie_id][] = $t;
            } else {
                $r[$t->id] = $t;
                $r[$t->id]->souscategorie = array();
            }
        }
        /* on met les sous catégories */
        foreach($s as $k => $v) {
            if(!array_key_exists($k, $r)) {
                continue;
            }
            $r[$k]->souscategorie = $v;
        }
        $view->setVariable('category', $r);
        $view->setVariable('flashMessages', $this->flashMessenger()->getMessages());
        return $view;
    }
    
    
    
    public function listProductAction(){
        /* recuperation de l'id category*/
        $id = (int) $this->params()->fromRoute('idCategorie', 0);
        if (!$id) {
            return $this->redirect()->toRoute('catalogue', array());
        }
        
        try {
            $category = $this->getServiceLocator()->get("CategoryTable")->getCategory($id);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('catalogue', array(
                'action' => 'index'
            ));
        }
        
        /* recuperation de la liste des produits rattachés */
        $view = new ViewModel();
        $res = $this->getServiceLocator()->get("ProductTable")->fetchAllByCategorie($id);
        
        $view->setVariable('flashMessages', $this->flashMessenger()->getMessages());
        $view->setVariable('produits', $res);
        $view->setVariable('categorie', $category);
        return $view;
    }
    public function addProductAction(){
        /* recuperation de l'id category*/
        $id = (int) $this->params()->fromRoute('idCategorie', 0);
        if (!$id) {
            return $this->redirect()->toRoute('catalogue', array());
        }
        
        try {
            $category = $this->getServiceLocator()->get("CategoryTable")->getCategory($id);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('catalogue', array(
                'action' => 'index'
            ));
        }
        
        $form = new \Application\Form\ProductForm(array(
            "type_embouteillage" => $this->getServiceLocator()->get("TypeEmbouteillageTable")->fetchAllActiveArray(), 
            "type_colisage" => $this->getServiceLocator()->get("TypeColisageTable")->fetchAllActiveArray(),
            "categorie" => $this->getServiceLocator()->get("CategoryTable")->fetchAllActiveArray(),
        ));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $obj = new \Application\Model\Product();
            $form->setInputFilter($obj->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $obj->exchangeArray($data);
                
                /* telechargement du fichier */
                $httpadapter = new \Zend\File\Transfer\Adapter\Http(); 
                //$filesize  = new \Zend\Validator\File\Size(array('min' => 1000 )); //1KB  
                //$extension = new \Zend\Validator\File\Extension(array('extension' => array('txt')));
                //$httpadapter->setValidators(array($filesize, $extension), $request->getFiles()->toArray()['image']['name']);
                $httpadapter->setDestination('public/data/product/');
                $newfile = "";
                if($httpadapter->receive()) {
                    $newfile = substr($httpadapter->getFileName(), 7); 
                }
                
                if($newfile != "") {
                    $obj->image = $newfile;
                }
                
                /* calcul du montant_total */
                $t = $obj->prix_base + ((!empty($obj->montant_taxe1))?$obj->montant_taxe1:0) + ((!empty($obj->montant_taxe2))?$obj->montant_taxe2:0) + ((!empty($obj->montant_tva))?$obj->montant_tva:0);
                $obj->montant_total = $t;
                
                /* sauvegarde + ajout du rattachement à la catégorie */
                $this->getServiceLocator()->get("ProductTable")->save($obj, $this->getServiceLocator()->get('user_service')->getLoggedUser(), $data["categorie_id"]);
                
                // Redirect to list of albums
                $this->flashMessenger()->addMessage("Ajout du produit réussi");
                return $this->redirect()->toRoute('catalogue/listproduit', array(
                    'idCategorie' => $id
                ));
            }
        }
        return array('form' => $form, "idCategorie" => $id);
    }
    public function editProductAction(){
        /* recuperation de l'id category*/
        $id = (int) $this->params()->fromRoute('idCategorie', 0);
        if (!$id) {
            return $this->redirect()->toRoute('catalogue', array());
        }
        
        try {
            $category = $this->getServiceLocator()->get("CategoryTable")->getCategory($id);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('catalogue', array(
                'action' => 'index'
            ));
        }
        
        $idProduct = (int) $this->params()->fromRoute('idProduct', 0);
        if (!$idProduct) {
            return $this->redirect()->toRoute('catalogue', array(
                'action' => 'addProduct', "idCategorie" => $id
            ));
        }
        try {
            $product = $this->getServiceLocator()->get("ProductTable")->getProduct($idProduct);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('catalogue', array(
                'action' => 'index'
            ));
        }
        
        $form = new \Application\Form\ProductForm(array(
            "type_embouteillage" => $this->getServiceLocator()->get("TypeEmbouteillageTable")->fetchAllActiveArray(), 
            "type_colisage" => $this->getServiceLocator()->get("TypeColisageTable")->fetchAllActiveArray(),
            "categorie" => $this->getServiceLocator()->get("CategoryTable")->fetchAllActiveArray(),
        ));
        
        /* Rajout des catégories liés au produit */
        $product->categorie_id = $this->getServiceLocator()->get('ProductTable')->listeCategorieAsArray($product);
        
        $form->bind($product);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $obj = new \Application\Model\Product();
            $form->setInputFilter($obj->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );
                $obj->exchangeArray($data);
                
                /* telechargement du fichier */
                $httpadapter = new \Zend\File\Transfer\Adapter\Http(); 
                $httpadapter->setDestination('public/data/product/');
                $newfile = "";
                if($httpadapter->receive()) {
                    $newfile = substr($httpadapter->getFileName(), 7); 
                }
                
                if($newfile != "") {
                    $obj->image = $newfile;
                }
                
                /* calcul du montant_total */
                $t = $obj->prix_base + ((!empty($obj->montant_taxe1))?$obj->montant_taxe1:0) + ((!empty($obj->montant_taxe2))?$obj->montant_taxe2:0) + ((!empty($obj->montant_tva))?$obj->montant_tva:0);
                $obj->montant_total = $t;
                
                /* sauvegarde + ajout du rattachement à la catégorie */
                $this->getServiceLocator()->get("ProductTable")->save($obj, $this->getServiceLocator()->get('user_service')->getLoggedUser(), $data["categorie_id"]);
                
                // Redirect to list of albums
                $this->flashMessenger()->addMessage("Edition du produit réussie");
                return $this->redirect()->toRoute('catalogue/listproduit', array(
                    'idCategorie' => $id
                ));
            }
        }
        return array('form' => $form, "idCategorie" => $id, "idProduct" => $idProduct);
    }
    public function delProductAction(){
        $id = (int) $this->params()->fromRoute('idCategorie', 0);
        if (!$id) {
            return $this->redirect()->toRoute('catalogue', array());
        }
        
        try {
            $category = $this->getServiceLocator()->get("CategoryTable")->getCategory($id);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('catalogue', array(
                'action' => 'index'
            ));
        }
        
        $idProduct = (int) $this->params()->fromRoute('idProduct', 0);
        if (!$idProduct) {
            return $this->redirect()->toRoute('catalogue', array(
                'action' => 'index'
            ));
        }
        try {
            $product = $this->getServiceLocator()->get("ProductTable")->getProduct($idProduct);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('catalogue', array(
                'action' => 'index'
            ));
        }            
        
        $this->getServiceLocator()->get("ProductTable")->delete($idProduct, $this->getServiceLocator()->get('user_service')->getLoggedUser());
        $this->flashMessenger()->addMessage("Suppression du produit réussie");
        return $this->redirect()->toRoute('catalogue/listproduit', array(
            'idCategorie' => $id
        ));
    }
    
    public function addCategoryAction(){
        $form = new \Application\Form\CategoryForm(array(
            "category" => $this->getServiceLocator()->get("CategoryTable")->fetchAllActiveArray(), 
        ));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $obj = new Category();
            $form->setInputFilter($obj->getInputFilter());
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($post);
            if ($form->isValid()) {
                $obj->exchangeArray($form->getData());
                
                /* telechargement du fichier */
                $httpadapter = new \Zend\File\Transfer\Adapter\Http(); 
                //$filesize  = new \Zend\Validator\File\Size(array('min' => 1000 )); //1KB  
                //$extension = new \Zend\Validator\File\Extension(array('extension' => array('txt')));
                //$httpadapter->setValidators(array($filesize, $extension), $request->getFiles()->toArray()['image']['name']);
                $httpadapter->setDestination('public/data/picto/');
                $newfile = "";
                if($httpadapter->receive()) {
                    $newfile = substr($httpadapter->getFileName(), 7); 
                }
                
                if($newfile != "") {
                    $obj->image = $newfile;
                }
                
                $this->getServiceLocator()->get("CategoryTable")->save($obj, $this->getServiceLocator()->get('user_service')->getLoggedUser());
                
                // Redirect to list of albums
                $this->flashMessenger()->addMessage("Ajout de la catégorie réussi");
                return $this->redirect()->toRoute('catalogue');
            }
        }
        return array('form' => $form);
    }
    public function editCategoryAction(){
        $id = (int) $this->params()->fromRoute('idCategorie', 0);
        if (!$id) {
            return $this->redirect()->toRoute('catalogue', array(
                'action' => 'addCategory'
            ));
        }
        // Get the User with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $category = $this->getServiceLocator()->get("CategoryTable")->getCategory($id);
        } catch (\Exception $ex) {
            $this->flashMessenger()->addMessage($ex->getMessage());
            return $this->redirect()->toRoute('catalogue', array(
                'action' => 'index'
            ));
        }
        $form = new \Application\Form\CategoryForm(array(
            "category" => $this->getServiceLocator()->get("CategoryTable")->fetchAllActiveArray(), 
        ));
        $form->bind($category);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($category->getInputFilter());
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($post);
            if ($form->isValid()) {
                
                /* telechargement du fichier */
                $httpadapter = new \Zend\File\Transfer\Adapter\Http(); 
                $httpadapter->setDestination('public/data/picto/');
                $newfile = "";
                if($httpadapter->receive()) {
                    $newfile = substr($httpadapter->getFileName(),7); 
                }
                
                if($newfile != "") {
                    $category->image = $newfile;
                } else {
                    $t2 = $this->getServiceLocator()->get("CategoryTable")->getCategory($id);
                    $category->image = $t2->image;
                }
                
                $this->getServiceLocator()->get("CategoryTable")->save($category, $this->getServiceLocator()->get('user_service')->getLoggedUser());

                // Redirect to list of albums
                $this->flashMessenger()->addMessage("Sauvegarde de la catégorie réussie");
                return $this->redirect()->toRoute('catalogue');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }
    public function delCategoryAction(){
        /* Possible uniquement s'il n'y a plus de produits rattachés */
        $id = (int) $this->params()->fromRoute('idCategorie', 0);
        if (!$id) {
            return $this->redirect()->toRoute('member');
        }
        
        $r = $this->getServiceLocator()->get("CategoryTable")->fetchAll(array("categorie_id" => $id));
        if(is_object($r->current())) {
            $this->flashMessenger()->addMessage("Suppression impossible car des catégories sont rattachés");
            return $this->redirect()->toRoute('catalogue');
        }
        
        $r = $this->getServiceLocator()->get("CategoryTable")->testProduct($id);
        if(is_object($r->current())) {
            $this->flashMessenger()->addMessage("Suppression impossible car des produits sont rattachés");
            return $this->redirect()->toRoute('catalogue');
        }
            
        
        $this->getServiceLocator()->get("CategoryTable")->delete($id, $this->getServiceLocator()->get('user_service')->getLoggedUser());
        $this->flashMessenger()->addMessage("Suppression de la catégorie réussie");
        return $this->redirect()->toRoute('catalogue');
    } 
}
