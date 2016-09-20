<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class StockTable
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
    public function fetchAllMouvement(User $user){
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->columns(array("*"));
        $select->from('historique_e_s');
        $select->join('stock','stock.id = historique_e_s.stock_id', array(), "inner");
        $select->join('historique_e_s_produit', 'historique_e_s.id = historique_e_s_id', array("quantite"), "inner");
        $select->join('produit', 'produit.id = historique_e_s_produit.produit_id', array("prix_base"), "inner");
        $select->join('user', 'user.id = historique_e_s.user_id', array("name","firstname"), "inner");
        $select->where(array("stock.user_id" => $user->id));
        $select->order("historique_e_s.created_at DESC");

        return $this->tableGateway->selectWith($select);
    }
    public function fetchMouvement(User $user, $id){
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->columns(array("*"));
        $select->from('historique_e_s_produit');
        $select->join('produit', 'produit.id = historique_e_s_produit.produit_id', array("libelle", "prix_base"), "inner");
        $select->where(array("historique_e_s_id" => $id));

        return $this->tableGateway->selectWith($select);
    }
    public function fetchAllSocieteActiveArray($libelle = null, $withIdUser = false, $orderby = null)
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
        
        if($orderby != null) {
            $select->order($orderby);
        }
        
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
    public function getStock($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    public function getStockByUserProd($idU, $idP)
    {
        $idU  = (int) $idU;
        $idP  = (int) $idP;
        $rowset = $this->tableGateway->select(array('user_id' => $idU, 'produit_id' => $idP));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }
    public function save(Stock $stock, User $user2 = null)
    {
        $data = array(
            'produit_id' => $stock->produit_id,
            'user_id' => $stock->user_id,
            'quantite' => $stock->quantite,
            'updated_by' => ((is_object($user2))?$user2->id:2),
        );
        
        $id = (int) $stock->id;
        $new_id = $id;
        if ($id == 0) {
            $d = new \DateTime();
            $data['created_at'] = $d->format('Y-m-d H:i:s');
            $this->tableGateway->insert($data);
            $new_id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getStock($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Stock id does not exist');
            }
        }
        
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("historique", $adapter);
        $fac->insert(array(
            "user_id" => ((is_object($user2))?$user2->id:2),
            "object" => "Stock",
            "object_id"=> $new_id,
            "commentaire"=> ((($id == 0)?"Création ":"Modification ").". Data: ".json_encode($data)),
            "action_date" => date('Y-m-d H:i:s')
        ));
    }
    public function saveHistorique($motif, User $boss, User $user){
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("historique_e_s", $adapter);
        $fac->insert(array(
            "user_id" => $user->id,
            "motif" => $motif,
            "stock_id" => new \Zend\Db\Sql\Predicate\Expression("(select id from stock where user_id =".$boss->id." LIMIT 1)")
        ));
        return $fac->getLastInsertValue();
    }
    public function saveHistoriqueProduct($id_historique, $id_product, $quantite){
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("historique_e_s_produit", $adapter);
        $fac->insert(array(
            "historique_e_s_id" => $id_historique,
            "produit_id" => $id_product,
            "quantite" => $quantite
        ));
        return;
    }
    public function delete($idStock, User $user = null)
    {
        $this->tableGateway->delete(array('id'=>$idStock));
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("historique", $adapter);
        $fac->insert(array(
            "user_id" => ((is_object($user))?$user->id:2),
            "object" => "Stock",
            "object_id"=> $idStock,
            "commentaire"=> "Delete.",
            "action_date" => date('Y-m-d H:i:s')
        ));
    }
 }