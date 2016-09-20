<?php
namespace RestApi\Controller;
 
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
 
class CategorieController extends AbstractRestfulController
{
    public function get($id)
    {
        /* ici, nous allons recherche les informations sur cette categorie, et lister tous les sous catégories, ainsi que tous produits */
        $request = $this->getRequest();
        $data = $request->getQuery();
        $response = $this->getResponseWithHeader();
        /* Verification du token */
		try{
            if(!array_key_exists("token", $data)) {
                throw new \Exception("Invalid parameters");
            }
            
            /*TODO : verification du token */
            $user = $this->getServiceLocator()->get("UserTable")->getByToken($data["token"]);
            if(!is_object($user)) {
                throw new \Exception("Invalid parameters.");
            }
            
            $obj = $this->getServiceLocator()->get("CategoryTable")->getCategory($id);
            $obj->list_produit = array();
            
            if(array_key_exists("action", $data)){
                if($data['action'] == "next") {
                    return $this->_getNextCategorie($id);
                }
                if($data['action'] == "previous") {
                    return $this->_getPreviousCategorie($id);
                }
            }
            
            
            $tab = $this->getServiceLocator()->get("CategoryTable")->fetchAll(array("categorie_id = ".$id), "libelle");
            
            $return = array();
            foreach ($tab as $i) {
                $t = $this->getServiceLocator()->get("ProductTable")->fetchAllByCategorie($i->id);
                $t2 = array();
                foreach($t as $i) {
                    $t2[] = $i;
                }
                $obj->list_produit[] = array(
                    "ss_cat" => $i,
                    "produit" => $t2
                );
            }
            
            /* ajout de produit liés directement à la catégorie mère */
            $t = $this->getServiceLocator()->get("ProductTable")->fetchAllByCategorie($id);
            $t2 = array();
            foreach($t as $i) {
                $t2[] = $i;
            }
            if(count($t2)>0){
                $obj->list_produit[] = array(
                    "ss_cat" => null,
                    "produit" => $t2
                );
            }
            
            $response->setContent($_GET['callback'].'('.json_encode(array("data" => $obj)).')');
        }catch( \Exception $e) {
            $response->setStatusCode(200)->setContent($_GET['callback'].'('."ko : ".$e->getMessage().')');
        }
        
        $response = $this->getResponseWithHeader();
        return $response; 
    }
     
    private function _getNextCategorie($id) {
        /* ici, nous allons recherche les informations sur cette categorie, et lister tous les sous catégories, ainsi que tous produits */
        $request = $this->getRequest();
        $data = $request->getQuery();
        $response = $this->getResponseWithHeader();
        /* Verification du token */
		try{
            $obj = $this->getServiceLocator()->get("CategoryTable")->getNextCategory($id);
            
            $response->setContent($_GET['callback'].'('.json_encode(array("data" => $obj)).')');
        }catch( \Exception $e) {
            $response->setStatusCode(200)->setContent($_GET['callback'].'('."ko : ".$e->getMessage().')');
        }
        
        $response = $this->getResponseWithHeader();
        return $response;
    }
    
    private function _getPreviousCategorie($id) {
        /* ici, nous allons recherche les informations sur cette categorie, et lister tous les sous catégories, ainsi que tous produits */
        $request = $this->getRequest();
        $data = $request->getQuery();
        $response = $this->getResponseWithHeader();
        /* Verification du token */
		try{
            $obj = $this->getServiceLocator()->get("CategoryTable")->getPreviousCategory($id);
            
            $response->setContent($_GET['callback'].'('.json_encode(array("data" => $obj)).')');
        }catch( \Exception $e) {
            $response->setStatusCode(200)->setContent($_GET['callback'].'('."ko : ".$e->getMessage().')');
        }
        
        $response = $this->getResponseWithHeader();
        return $response;
    }
    
    public function getList()
    {
        $request = $this->getRequest();
        $data = $request->getQuery();
        $response = $this->getResponseWithHeader();
        /* Verification du token */
		try{
            if(!array_key_exists("token", $data)) {
                throw new \Exception("Invalid parameters");
            }
            
            /*TODO : verification du token */
            $user = $this->getServiceLocator()->get("UserTable")->getByToken($data["token"]);
            if(!is_object($user)) {
                throw new \Exception("Invalid parameters.");
            }
            
            $tab = $this->getServiceLocator()->get("CategoryTable")->fetchAll(array("categorie_id IS NULL"), "libelle");
            
            $return = array();
            foreach ($tab as $i) {
                $return[] = array(
                    "id" => $i->id,
                    "libelle" => $i->libelle
                );
            }
            
            $response->setContent($_GET['callback'].'('.json_encode(array("data" => $return)).')');
        }catch( \Exception $e) {
            $response->setStatusCode(200)->setContent($_GET['callback'].'('."ko : ".$e->getMessage().')');
        }
        
        $response = $this->getResponseWithHeader();
        return $response; 
    }
     
    public function create($data)
    {
        $response = $this->getResponseWithHeader()->setStatusCode(403);
        return $response;
    }
     
    public function update($id, $data)
    {
        $response = $this->getResponseWithHeader()->setStatusCode(403);
        return $response;
    }
     
    public function delete($id)
    {
        $response = $this->getResponseWithHeader()->setStatusCode(403);
        return $response;
    }
     
    // configure response
    public function getResponseWithHeader()
    {
        $response = $this->getResponse();
        $response->getHeaders()
                 //make can accessed by *   
                 ->addHeaderLine('Access-Control-Allow-Origin','*')
                 //set allow methods
                 ->addHeaderLine('Access-Control-Allow-Methods','POST PUT DELETE GET')
				 ->addHeaderLine('Content-type','application/json');
         
        return $response;
    }
}