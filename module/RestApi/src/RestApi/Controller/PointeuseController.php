<?php
namespace RestApi\Controller;
 
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
 
class PointeuseController extends AbstractRestfulController
{
    public function get($id)
    {
        $request = $this->getRequest();
        $data = $request->getQuery();
        $response = $this->getResponseWithHeader();
        /* Verification du token */
		try{
            if(!array_key_exists("token", $data)
                || !array_key_exists("action", $data)
                || !in_array($data["action"], array("entree", "sortie"))) {
                throw new \Exception("Invalid parameters");
            }
            $user = $this->getServiceLocator()->get("UserTable")->getByLogin($id);
            
            if(!is_object($user)) {
                throw new \Exception("Invalid parameters.");
            }
            if(empty($user->token)|| $user->token != $data["token"]) {
                throw new \Exception("Invalid parameters..");
            }
            
            $date = new \DateTime();
            
            /* dans le cas d'une entree on creer un nouvel objet pointage */
            if($data['action'] == "entree") {
                $obj = new \Application\Model\Pointage();
                $obj->user_id = $user->id;
                $obj->date_debut = $date->format('Y-m-d H:i:00');
                $this->getServiceLocator()->get("PointageTable")->save($obj, $user);
            }
            /* 
             * dans le cas d'une sortie, nous allons cherche la dernière entrée, 
             * et nous regardons si elle a une date de sortie 
             */
            else {
                $obj = $this->getServiceLocator()->get("PointageTable")->getLastPointageUser($user);
                if($obj == null || !is_object($obj) || !empty($obj->date_fin)) {
                    $obj = new \Application\Model\Pointage();
                    $obj->user_id = $user->id;
                }
                $obj->date_fin = $date->format('Y-m-d H:i:00');
                $this->getServiceLocator()->get("PointageTable")->save($obj, $user);
            }
            
            $response->setContent($_GET['callback'].'('.json_encode(array("result" => "ok")).')');
        }catch( \Exception $e) {
            $response->setStatusCode(200)->setContent($_GET['callback'].'('."ko : ".$e->getMessage().')');
        }
        
        $response = $this->getResponseWithHeader();
        return $response;
    }
     
    public function getList()
    {
        $response = $this->getResponseWithHeader()->setStatusCode(403);
        return $response;
    }
     
    public function create($data)
    {
        $response = $this->getResponseWithHeader();
        /* Verification du token */
        try{
            if(!array_key_exists("login", $data)
                || !array_key_exists("token", $data)
                || !array_key_exists("action", $data)
                || !in_array($data["action"], array("entree", "sortie"))) {
                throw new \Exception("Invalid parameters");
            }
            $user = $this->getServiceLocator()->get("UserTable")->getByLogin($data['login']);
            
            if(!is_object($user)) {
                throw new \Exception("Invalid parameters.");
            }
            if(empty($user->token)|| $user->token != $data["token"]) {
                throw new \Exception("Invalid parameters..");
            }
            
            $date = new \DateTime();
            
            /* dans le cas d'une entree on creer un nouvel objet pointage */
            if($data['action'] == "entree") {
                $obj = new \Application\Model\Pointage();
                $obj->user_id = $user->id;
                $obj->date_debut = $date->format('Y-m-d H:i:00');
                $this->getServiceLocator()->get("PointageTable")->save($obj, $user);
            }
            /* 
             * dans le cas d'une sortie, nous allons cherche la dernière entrée, 
             * et nous regardons si elle a une date de sortie 
             */
            else {
                $obj = $this->getServiceLocator()->get("PointageTable")->getLastPointageUser($user);
                if($obj == null || !is_object($obj) || !empty($obj->date_fin)) {
                    $obj = new \Application\Model\Pointage();
                    $obj->user_id = $user->id;
                }
                $obj->date_fin = $date->format('Y-m-d H:i:00');
                $this->getServiceLocator()->get("PointageTable")->save($obj, $user);
            }
            
            $response->setContent($_GET['callback'].'('.json_encode(array("result" => "ok")).')');
        }catch( \Exception $e) {
            $response->setStatusCode(200)->setContent($_GET['callback'].'('."ko : ".$e->getMessage().')');
        }
        
        $response = $this->getResponseWithHeader();
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