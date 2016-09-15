<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class HistoriqueTable
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
    /*public function fetchAllActiveArray()
    {
        $return = array();
        $resultSet = $this->tableGateway->select(array('published' => "1"));
        foreach($resultSet as $item) {
            $return[$item->id] = $item->name;
        }
        return $return;
    }*/
    
    public function fetchAllBy($where = array(), $field = "", $direction = "ASC", $limit = null)
    {
        return $this->tableGateway->select(function(\Zend\Db\Sql\Select $select) use ($where, $field, $direction, $limit) {
            $select->where($where);
            $select->order($field. " " . $direction);
            if($limit != null){
                $select->limit($limit);
            }
        });
    }
 }