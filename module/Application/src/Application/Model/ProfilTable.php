<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class ProfilTable
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
    public function fetchAllActiveArray($where = array())
    {
        $return = array();
        $resultSet = $this->tableGateway->select($where);
        foreach($resultSet as $item) {
            $return[$item->id] = $item->libelle;
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
 }