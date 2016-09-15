<?php
 
namespace Application\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use \Zend\InputFilter\InputFilter;

class Profil
{
    public $id;
    public $libelle;
    
    protected $inputFilter;  

    public function exchangeArray($data)
    {
        $this->id          = (!empty($data['id'])) ? $data['id'] : null;
        $this->libelle         = (!empty($data['libelle'])) ? $data['libelle'] : null;
    }
}