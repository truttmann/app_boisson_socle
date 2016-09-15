<?php
 
namespace Application\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use \Zend\InputFilter\InputFilter;

class Stock implements InputFilterAwareInterface
{
    public $id;
    public $user_id;
    public $produit_id;
    public $quantite;
    public $created_at;
    public $updated_at;    
    public $updated_by;
    
    protected $inputFilter;  

    public function exchangeArray($data)
    {
        $this->id          = (!empty($data['id'])) ? $data['id'] : null;
        $this->user_id         = (!empty($data['user_id'])) ? $data['user_id'] : null;
        $this->produit_id         = (!empty($data['produit_id'])) ? $data['produit_id'] : null;
        $this->quantite         = (!empty($data['quantite'])) ? $data['quantite'] : null;
        $this->created_at         = (!empty($data['created_at'])) ? $data['created_at'] : null;
        $this->updated_at         = (!empty($data['updated_at'])) ? $data['updated_at'] : null;
        $this->updated_by   = (!empty($data['updated_by'])) ? $data['updated_by'] : null;
    }
    
    // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    // Add content to these methods:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'id',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'quantite',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'produit_id',
                'required' => True,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Int'
                    ),
                ),
            ));
            

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}