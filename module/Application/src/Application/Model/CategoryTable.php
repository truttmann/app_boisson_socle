<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class CategoryTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function fetchAll($where = array(), $orderby = null)
    {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('categorie');
        if(is_array($where) && count($where) != 0){
            $select->where($where);
        }
        if(!empty($orderby)){
            $select->order(array($orderby));        
        }
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
    public function fetchAllActiveArray($where = array())
    {
        $return = array();
        $resultSet = $this->tableGateway->select($where);
        foreach($resultSet as $item) {
            $return[$item->id] = $item->libelle;
        }
        return $return;
    }
    public function getCategory($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    public function getNextCategory($id)
    {
        $id  = (int) $id;
        
        $rowset = $this->tableGateway->select(function(\Zend\Db\Sql\Select $select) use($id){
            $select->where->greaterThan('id', $id);   
            $select->where->isNull('categorie_id');
        });
        $row = $rowset->current();
        if (!$row) {
            return $this->getFirstCategoryParent($id);
        }
        return $row;
    }
    public function getPreviousCategory($id)
    {
        $id  = (int) $id;
        
        $rowset = $this->tableGateway->select(function(\Zend\Db\Sql\Select $select) use($id){
            $select->where->lessThan('id', $id);  
            $select->where->isNull('categorie_id');
        });
        $row = $rowset->current();
        if (!$row) {
            return $this->getLastCategoryParent($id);
        }
        return $row;
    }
    public function getLastCategoryParent($id)
    {
        $id  = (int) $id;
        
        $rowset = $this->tableGateway->select(function(\Zend\Db\Sql\Select $select) use($id){
            $select->where->notEqualTo('id', $id);    
            $select->where->isNull('categorie_id');
            $select->order('id DESC');
        });
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    
    public function getFirstCategoryParent($id)
    {
        $id  = (int) $id;
        
        $rowset = $this->tableGateway->select(function(\Zend\Db\Sql\Select $select) use($id){
            $select->where->notEqualTo('id', $id); 
            $select->where->isNull('categorie_id');            
            return $select->order('id ASC');
        });
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    
    public function testProduct($id)
    {
        $id  = (int) $id;
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('produit_categorie');
        $select->where(array("categorie_id" => $id));
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
    public function save(Category $category, User $user = null)
    {
        $data = array(
            'libelle' => $category->libelle,
            'description' => $category->description,
            'image' => $category->image,
            'categorie_id' => $category->categorie_id,
        );
        
        $id = (int) $category->id;
        $new_id = $id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $new_id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getCategory($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Category id does not exist');
            }
        }
        
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("historique", $adapter);
        $fac->insert(array(
            "user_id" => ((is_object($user))?$user->id:2),
            "object" => "Categorie",
            "object_id"=> $new_id,
            "commentaire"=> ((($id == 0)?"Création ":"Modification ").". Data: ".json_encode($data)),
            "action_date" => date('Y-m-d H:i:s')
        ));
    }
    public function delete($id, User $user = null)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
        
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("historique", $adapter);
        $fac->insert(array(
            "user_id" => ((is_object($user))?$user->id:2),
            "object" => "Categorie",
            "object_id"=> $id,
            "commentaire"=> "Delete.",
            "action_date" => date('Y-m-d H:i:s')
        ));
    }
 }