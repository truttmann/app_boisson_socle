<?php
namespace RestApi\Controller;
 
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
 
class MemberController extends AbstractRestfulController
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
        $response = $this->getResponseWithHeader();
        try {
            $user = $this->getServiceLocator()->get("UserTable")->getUser($id);
            $response->setContent($_GET['callback'].'('.json_encode(array("data" =>$user)).')');
        } catch (\Exception $ex) {
            $response->setStatusCode(200)->setContent($_GET['callback'].'('.'"ko : Undefined User"'.')');
        }

        return $response;
    }
     
    public function getList()
    {
        $response = $this->getResponseWithHeader();
        try {
            if(!array_key_exists('p', $_GET)) {
                throw new \Exception('bad parameter');
            }
            $user = $this->getServiceLocator()->get("UserTable")->getByToken($_GET["p"]);
            
            if($user->published != 1) {
                throw new \Exception("User unpublished");
            }
            
            $users = $this->getServiceLocator()->get("UserTable")->fetchAll(array("societe" => $user->societe, "profil_id" => 2));
            $r = array();
            foreach($users as $i) {
                $r[] = $i;
            }
            $response->setContent($_GET['callback'].'('.json_encode(array("data" =>$r)).')');
        } catch (\Exception $ex) {
            $response->setStatusCode(200)->setContent($_GET['callback'].'('.'Undefined User : '.$ex->getMessage().')');
        }

        
        return $response;
    }
     
    public function create($data)
    {
        $u2 = $this->getServiceLocator()->get("UserTable")->getByToken($data['token']);
        if(! is_object($u2)){
            $response = $this->getResponseWithHeader()->setStatusCode(200);
            $response->setContent($_GET['callback'].'('.json_encode("unable to find user by token").')');
            return $response;
        }
        $u = new \Application\Model\User();
        $u->name = $data['name'];
        $u->firstname = $data['firstname'];
        $u->societe = $u2->societe;
        $u->email = $data['email'];
        $u->profil_id = 2;
        $u->published = 1;
        $u->fonction = $data['fonction'];
        $u->telephone = $data['telephone'];
        $u->droit_mobile = $data['droit_mobile'];
        $u = $this->getServiceLocator()->get("UserTable")->save($u, $u2);
        
        $response = $this->getResponseWithHeader()->setStatusCode(200);
        $response->setContent($_GET['callback'].'('.json_encode(array("data" => $u)).')');
        return $response;
    }
     
    public function update($id, $data)
    {
        $u = $this->getServiceLocator()->get("UserTable")->getUser($id);
        if(! is_object($u)){
            $response = $this->getResponseWithHeader()->setStatusCode(200);
            $response->setContent($_GET['callback'].'('.json_encode("unable to find user").')');
            return $response;
        }
        $u2 = $this->getServiceLocator()->get("UserTable")->getByToken($data['token']);
        if(! is_object($u2)){
            $response = $this->getResponseWithHeader()->setStatusCode(200);
            $response->setContent($_GET['callback'].'('.json_encode("unable to find user by token").')');
            return $response;
        }
        $u->id = $id;
        $u->name = $data['name'];
        $u->firstname = $data['firstname'];
        $u->email = $data['email'];
        $u->societe = $u2->societe;
        $u->profil_id = 2;
        $u->fonction = $data['fonction'];
        $u->telephone = $data['telephone'];
        $u->droit_mobile = $data['droit_mobile'];
        $u = $this->getServiceLocator()->get("UserTable")->save($u, $u2);
        
        $response = $this->getResponseWithHeader()->setStatusCode(200);
        $response->setContent($_GET['callback'].'('.json_encode(array("data" => $u)).')');
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