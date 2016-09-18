<?php
namespace RestApi\Controller;
 
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
 
class ProduitController extends AbstractRestfulController
{
    // methode liste, ex : "/wine"
	protected $collectionMethod = array('GET', 'POST'); 
	// methode unitaire, ex : "/wine?slug=sociando_mallet-42"
	protected $ressourceMethod = array('GET', 'POST', 'PUT', 'DELETE'); 
    
    protected $identifierName = 'id';
	
	public function setEventManager(\Zend\EventManager\EventManagerInterface $events) {
		parent::setEventManager($events);
		$events->attach('dispatch', array($this, 'checkMethod'), 10);
	}
	
	protected function _getMethod() {
        if ($this->params()->fromRoute('id', false)){
			return $this->ressourceMethod;
		}
		return $this->collectionMethod;
	}

	public function checkMethod($e) {
		if (in_array($e->getRequest()->getMethod(), $this->_getMethod())){
			return;
		}
		$response = $this->getResponse();
		$response->setStatusCode(405);
		return $response;
	}
	
	public function options() {
		$response = $this->getResponse();
		$response->getHeaders()
		->addHeaderLine('Allow', implode(',', $this->_getMethod()));
		return $response;
	}
    
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
            
            /* verification du token */
            $user = $this->getServiceLocator()->get("UserTable")->getByToken($data["token"]);
            if(!is_object($user)) {
                throw new \Exception("Invalid parameters.");
            }
            
            /* recuperation du boss */
            if($user->profil_id != 1) {
                $user = $this->getServiceLocator()->get("UserTable")->getBoss($user);
                if(!is_object($user)) {
                    throw new \Exception("Invalid parameters.");
                }
            }

            /* recherche des informations de quantite du stock entreprise des produits */
            $where = array();
            if(array_key_exists("l", $_GET)) {
                $t = explode ("_", $_GET['l']);
                $where[] = "produit.id IN (".implode(",", $t).")";
            }
            
            $tab = $this->getServiceLocator()->get("ProductTable")->fetchAllWithStock($user, $where, "libelle");
            
            $return = array();
            foreach ($tab as $i) {
                $return[] = array(
                    "id" => $i->id,
                    "libelle" => $i->libelle,
                    "prix_base" => $i->prix_base,
                    "quantite" => $i->quantite
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