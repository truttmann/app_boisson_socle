<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class UserTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function fetchAll($where = array())
    {
        $resultSet = $this->tableGateway->select($where);
        return $resultSet;
    }
    public function fetchAllActiveArray()
    {
        $return = array();
        $resultSet = $this->tableGateway->select(array('published' => "1"));
        foreach($resultSet as $item) {
            $return[$item->id] = $item->name." ".$item->firstname;
        }
        return $return;
    }
    public function fetchAllSocieteActiveArray($libelle = null, $withIdUser = false)
    {
        $return = array();
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->quantifier("DISTINCT")->columns(array('societe'), false);
        
        
        $where = array('profil_id' => 1);
        if($libelle != null){
            $where['societe'] = $libelle;
        } else {
            $where['published'] = '1';
        }
        $select->from('user')->where($where);
        
        $resultSet = $this->tableGateway->selectWith($select);
    
        foreach($resultSet as $item) {
            if($withIdUser) {
                $return[$item->id] = $item->societe;
            } else {
                $return[$item->societe] = $item->societe;
            }
        }
        
        return $return;
    }
    public function fetchAllSocieteArray($libelle = null, $withIdUser = false)
    {
        $return = array();
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->quantifier("DISTINCT")->columns(array('id', 'societe'), false);
        
        
        $where = array('profil_id' => 1);
        if($libelle != null){
            $where['societe'] = $libelle;
        }
        $select->from('user')->where($where);
        
        $resultSet = $this->tableGateway->selectWith($select);
    
        foreach($resultSet as $item) {
            if($withIdUser) {
                $return[$item->id] = $item->societe;
            } else {
                $return[$item->societe] = $item->societe;
            }
        }
        
        return $return;
    }
    public function getUser($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    public function getBoss(User $user)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('societe' => $user->societe, 'profil_id' => 1));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    public function getByLogin($login)
    {
        $rowset = $this->tableGateway->select(array('login' => $login));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Invalid login / password");
        }
        return $row;
    }
    public function getByToken($token)
    {
        $rowset = $this->tableGateway->select(array('token' => $token));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Invalid token");
        }
        return $row;
    }
    public function save(User $user, User $user2 = null)
    {
        $data = array(
            'name' => $user->name,
            'firstname' => $user->firstname,
            'email' => $user->email,
            'published' => $user->published,
            'societe' => $user->societe, 
            'profil_id' => $user->profil_id,
            'token' => $user->token,
            'adresse' => $user->adresse,
            'cp' => $user->cp,
            'ville' => $user->ville,
            'siret' => $user->siret,
            'tva' => $user->tva,
            'horaire' => $user->horaire,
            'information' => $user->information,
            'fonction' => $user->fonction,
            'telephone' => $user->telephone,
            'droit_mobile' => $user->droit_mobile,
            'updated_by' => ((is_object($user2))?$user2->id:2),
        );
        
        $id = (int) $user->id;
        $new_id = $id;
        if ($id == 0) {
            $d = new \DateTime();
            $data['created_at'] = $d->format('Y-m-d H:i:s');
            $this->tableGateway->insert($data);
            $new_id = $this->tableGateway->getLastInsertValue();
            $user->id = $new_id;
        } else {
            if ($this->getUser($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('User id does not exist');
            }
        }
        
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("historique", $adapter);
        $fac->insert(array(
            "user_id" => ((is_object($user2))?$user2->id:2),
            "object" => "User",
            "object_id"=> $new_id,
            "commentaire"=> ((($id == 0)?"Création ":"Modification ").". Data: ".json_encode($data)),
            "action_date" => date('Y-m-d H:i:s')
        ));
        
        return $user;
    }
    public function regeneratecle($id, $token, User $user = null)
    {
        $this->tableGateway->update(array('token'=>$token, 'updated_by' => ((is_object($user))?$user->id:2)), array('id' => (int) $id));
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("historique", $adapter);
        $fac->insert(array(
            "user_id" => ((is_object($user))?$user->id:2),
            "object" => "User",
            "object_id"=> $id,
            "commentaire"=> json_encode(array("token" => $token)),
            "action_date" => date('Y-m-d H:i:s')
        ));
    }
    public function delete($id, User $user = null)
    {
        $this->tableGateway->update(array('published'=>0,'password'=>null, 'updated_by' => ((is_object($user))?$user->id:2)), array('id' => (int) $id));
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("historique", $adapter);
        $fac->insert(array(
            "user_id" => ((is_object($user))?$user->id:2),
            "object" => "User",
            "object_id"=> $id,
            "commentaire"=> "Delete.",
            "action_date" => date('Y-m-d H:i:s')
        ));
    }
 }