<?php
 
namespace Application\Model;

class Historique
{
    public $id;
    public $action_date;
    public $user_id;
    public $object;
    public $object_id;
    public $commentaire;

    
    protected $inputFilter;  

    public function exchangeArray($data)
    {
        $this->id          = (!empty($data['id'])) ? $data['id'] : null;
        $this->action_date         = (!empty($data['action_date'])) ? $data['action_date'] : null;
        $this->user_id         = (!empty($data['user_id'])) ? $data['user_id'] : null;
        $this->object         = (!empty($data['object'])) ? $data['object'] : null;
        $this->object_id         = (!empty($data['object_id'])) ? $data['object_id'] : null;
        $this->commentaire   = (!empty($data['commentaire'])) ? $data['commentaire'] : null;
    }
    
    // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
}