<?php
namespace RestApi\Controller;
 
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
 
class StockController extends AbstractRestfulController
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
        $request = $this->getRequest();
        $data = $request->getQuery();
        $response = $this->getResponseWithHeader();
        /* Verification du token */
		try{
            /* verification du token */
            $user = $this->getServiceLocator()->get("UserTable")->getUser($id);
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
                        
            $tab = $this->getServiceLocator()->get("ProductTable")->fetchAllByStock($user, $where, "libelle");
            
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
        
        $request = $this->getRequest();
        $response = $this->getResponseWithHeader();
        /* Verification du token */
		try{
            /* verification du token */
            $user = $this->getServiceLocator()->get("UserTable")->getUser($id);
            if(!is_object($user)) {
                throw new \Exception("Invalid parameters.");
            }
            
            /* recuperation du boss */
            $boss = $user;
            if($user->profil_id != 1) {
                $boss = $this->getServiceLocator()->get("UserTable")->getBoss($user);
                if(!is_object($boss)) {
                    throw new \Exception("Invalid parameters.");
                }
            }

            /* recherche des informations de quantite du stock entreprise des produits */
            if(!array_key_exists('product', $data) || ! is_array($data['product']) || count($data['product']) == 0) {
                throw new \Exception("Invalid parameters.");
            }
            if(!array_key_exists('motif', $data) || empty($data['motif'])) {
                $data['motif'] = "livraison";
            }
            
            /* sauvegarde de l'historique */
            $h_id = $this->getServiceLocator()->get("StockTable")->saveHistorique($data['motif'], $boss, $user);
            
            /* parcours de tous les produits, et ajout au stock */
            foreach($data['product'] as $item) {
                $l = $this->getServiceLocator()->get("StockTable")->getStockByUserProd($boss->id, $item['id']);
                
                /* sauvegarde de l'historique */
                $this->getServiceLocator()->get("StockTable")->saveHistoriqueProduct($h_id, $item['id'], $item['qt']);
                
                /* modification des stock */
                if($l == null) {
                    /* element non présent dans le stock*/
                    $l = new \Application\Model\Stock();
                    $l->user_id = $boss->id;
                    $l->produit_id = $item['id'];
                    $l->quantite = $item['qt'];
                } else {
                    /* update */
                    $l->quantite = $l->quantite + $item['qt'];
                }
                $this->getServiceLocator()->get("StockTable")->save($l, $user);
            }
            
            $response->setContent($_GET['callback'].'('.json_encode(array("data" => "ok")).')');
        }catch( \Exception $e) {
            $response->setStatusCode(200)->setContent($_GET['callback'].'('."ko : ".$e->getMessage().')');
        }
        
        $response = $this->getResponseWithHeader();
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