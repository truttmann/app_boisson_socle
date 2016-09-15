<?php
namespace RestApi\Controller;
 
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
 
class UserController extends AbstractRestfulController
{
    // methode liste, ex : "/wine"
	protected $collectionMethod = array('GET', 'POST'); 
	// methode unitaire, ex : "/wine?slug=sociando_mallet-42"
	protected $ressourceMethod = array('GET', 'POST', 'PUT', 'DELETE'); 
    
    protected $identifierName = 'token';
	
	public function setEventManager(\Zend\EventManager\EventManagerInterface $events) {
		parent::setEventManager($events);
		$events->attach('dispatch', array($this, 'checkMethod'), 10);
	}
	
	protected function _getMethod() {
		if ($this->params()->fromRoute('token', false)){
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
    
    public function get($token)
    {
        $response = $this->getResponseWithHeader();
        try {
            $user = $this->getServiceLocator()->get("UserTable")->getByToken($token);
            if($user->published != 1) {
                throw new \Exception("User unpublished");
            }
            $response->setContent($_GET['callback'].'('.json_encode(array("data" =>$user)).')');
        } catch (\Exception $ex) {
            $response->setStatusCode(200)->setContent($_GET['callback'].'('.'"ko : Undefined User"'.')');
        }

        
        return $response;
    }
     
    public function getList()
    {
        $response = $this->getResponseWithHeader()->setStatusCode(403);
        return $response;
    }
     
    public function create($data)
    {
        /*
            nom
            prenom
            societe
            adr_societe
            cp
            ville
            siret
            tva
            horaire:
            information:
            email
            email_conf
            password
            password_conf
        */
        
        $u = new \Application\Model\User();
        $u->name = $data['nom'];
        $u->firstname = $data['prenom'];
        $u->email = $data['email'];
        $u->published = 0;
        $u->societe = $data['societe'];
        $u->profil_id = 1;
        $u->adresse = $data['adr_societe'];
        $u->cp = $data['cp'];
        $u->ville = $data['ville'];
        $u->siret = $data['siret'];
        $u->tva = $data['tva'];
        $u->horaire = $data['horaire'];
        $u->information = $data['information'];
        $u = $this->getServiceLocator()->get("UserTable")->save($u);
        
        
        
        // => TODO : que faire avec ces informations ?
        
        $response = $this->getResponseWithHeader()->setStatusCode(200);
        $response->setContent($_GET['callback'].'('.json_encode(array("data" => $u)).')');
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