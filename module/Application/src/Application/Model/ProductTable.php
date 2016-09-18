<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class ProductTable
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
    public function fetchAllWithStock(User $user, $where = array(), $orderby)
    {
        $return = array();
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        
        $select->from('produit');
        $select->join("stock", new \Zend\Db\Sql\Predicate\Expression("(stock.user_id = ".$user->id." AND stock.produit_id = produit.id)"), "quantite", "left");
        if(!empty($where)){
            $select->where($where);
        }        
        if($orderby != null) {
            $select->order($orderby);
        }
        
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
    public function fetchAllByStock(User $user, $where = array(), $orderby)
    {
        $return = array();
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        
        $select->from('produit');
        $select->join("stock", new \Zend\Db\Sql\Predicate\Expression("(stock.user_id = ".$user->id." AND stock.produit_id = produit.id)"), "quantite", "inner");
        if(!empty($where)){
            $select->where($where);
        }        
        if($orderby != null) {
            $select->order($orderby);
        }
        
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
    public function fetchAllByCategorie ($id) {
        $return = array();
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        
        $where = array('categorie_id' => $id);
        $select->from('produit')->join('produit_categorie', 'produit_id = produit.id', array(), 'inner')->where($where)->order(array("libelle ASC"));

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
    public function fetchAllActiveArray($where = array(), $orderby = null)
    {
        $return = array();
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        
        $select->from('produit');
        if(!empty($where)){
            $select->where($where);
        }        
        if($orderby != null) {
            $select->order($orderby);
        }
        
        $resultSet = $this->tableGateway->selectWith($select);
    
        foreach($resultSet as $item) {
            $return[$item->id] = $item->libelle;
        }
        return $return;
    }
    public function getProduct($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    public function save(Product $product, User $user = null, array $idsCategorie = array())
    {
        $data = array(
            'libelle' => $product->libelle,
            'description' => $product->description,
            'image' => $product->image,
            'prix_base' => $product->prix_base,
            'montant_taxe1' => $product->montant_taxe1,
            'montant_taxe2' => $product->montant_taxe2,
            'montant_tva' => $product->montant_tva,
            'montant_total' => $product->montant_total,
            'producteur' => $product->producteur,
            'contenance' => $product->contenance,
            'type_embouteillage_id' => $product->type_embouteillage_id,
            'type_colisage_id' => $product->type_colisage_id,
            'published' => $product->published,
        );
        
        $id = (int) $product->id;
        $new_id = $id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $new_id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getProduct($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Category id does not exist');
            }
        }
        
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("historique", $adapter);
        $fac->insert(array(
            "user_id" => ((is_object($user))?$user->id:2),
            "object" => "Produit",
            "object_id"=> $new_id,
            "commentaire"=> ((($id == 0)?"Création ":"Modification ").". Data: ".json_encode($data)),
            "action_date" => date('Y-m-d H:i:s')
        ));
        
        foreach ($idsCategorie as $idCategorie) {
            $adapter = $this->tableGateway->getAdapter();
            $fac2 = new TableGateway("produit_categorie", $adapter);
            $rowset = $fac2->select(array('produit_id' => $new_id, "categorie_id" => $idCategorie));
            $row = $rowset->current();
            if (!$row) {
                $fac2->insert(array(
                    'produit_id' => $new_id,
                    "categorie_id" => $idCategorie
                ));
            }
        }
    }
    public function listeCategorieAsArray(Product $product) {
        $return = array();
        $adapter = $this->tableGateway->getAdapter();
        $fac2 = new TableGateway("produit_categorie", $adapter);
        $resultSet = $fac2->select(array('produit_id' => $product->id));
        foreach($resultSet as $item) {
            $return[] = $item->categorie_id;
        }
        return $return;
    }
    public function delete($id, User $user = null)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
        
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("historique", $adapter);
        $fac->insert(array(
            "user_id" => ((is_object($user))?$user->id:2),
            "object" => "Produit",
            "object_id"=> $id,
            "commentaire"=> "Delete.",
            "action_date" => date('Y-m-d H:i:s')
        ));
    }
 }