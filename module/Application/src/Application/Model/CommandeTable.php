<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class CommandeTable
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
        $select->from('commande');
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
    public function getCommandeDetail($id)
    {
        $return = array();
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("commande_produit", $adapter);
        $select = new \Zend\Db\Sql\Select();
        $select->from("commande_produit");
        $select->where(array("commande_id" => (int) $id));
        $select->join('produit', 'produit_id = produit.id', array("libelle", "description", "prix_base"), 'inner');
        $resultSet = $fac->selectWith($select);
        foreach($resultSet as $item) {
            $return[] = $item;
        }
        return $return;
    }
    public function getCommande($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    public function isNumeroExist($numero)
    {
        $rowset = $this->tableGateway->select(array('numero' => $numero));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return true;
    }
    public function save(Commande &$commande, User $user = null)
    {
        $data = array(
            "numero" => $commande->numero ,
            "montant_ht" => $commande->montant_ht ,
            "montant_taxe1" => $commande->montant_taxe1 ,
            "montant_taxe2" => $commande->montant_taxe2 ,
            "montant_tva" => $commande->montant_tva ,
            "montant_ttc" => $commande->montant_ttc ,
            "reception_at" => $commande->reception_at ,
            "created_by" => $commande->created_by ,
            "created_for" => $commande->created_for ,
            "validation_at" => $commande->validation_at ,
            "status" => $commande->status ,
        );
        
        $id = (int) $commande->id;
        $new_id = $id;
        if ($id == 0) {
            $d = new \DateTime();
            $data['created_at'] = $d->format('Y-m-d H:i:s');
            $this->tableGateway->insert($data);
            $new_id = $this->tableGateway->getLastInsertValue();
            $commande->id = $new_id;
        } else {
            if ($this->getCommande($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Category id does not exist');
            }
        }
        
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("historique", $adapter);
        $fac->insert(array(
            "user_id" => ((is_object($user))?$user->id:2),
            "object" => "Commande",
            "object_id"=> $new_id,
            "commentaire"=> ((($id == 0)?"Création ":"Modification ").". Data: ".json_encode($data)),
            "action_date" => date('Y-m-d H:i:s')
        ));
        return $new_id;
    }
    public function delete($id, User $user = null)
    {
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("commande_produit", $adapter);
        $fac->delete(array("commande_id" => (int) $id));
        
        $this->tableGateway->delete(array('id' => (int) $id));
        
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("historique", $adapter);
        $fac->insert(array(
            "user_id" => ((is_object($user))?$user->id:2),
            "object" => "Commande",
            "object_id"=> $id,
            "commentaire"=> "Delete.",
            "action_date" => date('Y-m-d H:i:s')
        ));
    }
    public function removeAllProduct(Commande $commande, User $user = null) {
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("commande_produit", $adapter);
        $fac->delete(array("commande_id" => (int) $commande->id));
        
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("historique", $adapter);
        $fac->insert(array(
            "user_id" => ((is_object($user))?$user->id:2),
            "object" => "Commande_produit",
            "object_id"=> $commande->id,
            "commentaire"=> "Delete _ produit.",
            "action_date" => date('Y-m-d H:i:s')
        ));
    }
    public function addProduct(Commande $commande, Product $product, $valeur, User $user = null){
        $data = array(
            "produit_id" => $product->id,
            "commande_id" => $commande->id,
            "quantite" => $valeur
        );
        $adapter = $this->tableGateway->getAdapter();
        $fac = new TableGateway("commande_produit", $adapter);
        $fac->insert($data);
        $new_id = $fac->getLastInsertValue();
        
        $adapter = $this->tableGateway->getAdapter();
        $fac2 = new TableGateway("historique", $adapter);
        $fac2->insert(array(
            "user_id" => ((is_object($user))?$user->id:2),
            "object" => "Commande_produit",
            "object_id"=> $new_id,
            "commentaire"=> "Création. Data: ".json_encode($data),
            "action_date" => date('Y-m-d H:i:s')
        ));
    }
 }