<?php
 
namespace Application\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use \Zend\InputFilter\InputFilter;

class User implements InputFilterAwareInterface
{
    public $id;
    public $name;
    public $firstname;
    public $email;
    public $published;
    public $societe;    
    public $profil;
    public $token;
    public $adresse;
    public $cp;
    public $ville;
    public $siret;
    public $tva;
    public $horaire;
    public $information;
    public $fonction;
    public $telephone;
    public $droit_mobile;
    
    protected $inputFilter;  

    public function exchangeArray($data)
    {
        $this->id          = (!empty($data['id'])) ? $data['id'] : null;
        $this->name         = (!empty($data['name'])) ? $data['name'] : null;
        $this->firstname         = (!empty($data['firstname'])) ? $data['firstname'] : null;
        $this->email         = (!empty($data['email'])) ? $data['email'] : null;
        $this->login         = (!empty($data['login'])) ? $data['login'] : null;
        $this->password         = (!empty($data['password'])) ? $data['password'] : null;
        $this->published   = (!empty($data['published'])) ? $data['published'] : null;
        $this->societe   = (!empty($data['societe'])) ? $data['societe'] : null;
        $this->profil_id   = (!empty($data['profil_id'])) ? $data['profil_id'] : null;
        $this->token   = (!empty($data['token'])) ? $data['token'] : null;
        $this->published   = (!empty($data['published'])) ? $data['published'] : 0;
        $this->adresse   = (!empty($data['adresse'])) ? $data['adresse'] : null;
        $this->cp   = (!empty($data['cp'])) ? $data['cp'] : null;
        $this->ville   = (!empty($data['ville'])) ? $data['ville'] : null;
        $this->siret   = (!empty($data['siret'])) ? $data['siret'] : null;
        $this->tva   = (!empty($data['tva'])) ? $data['tva'] : null;
        $this->horaire   = (!empty($data['horaire'])) ? $data['horaire'] : null;
        $this->information   = (!empty($data['information'])) ? $data['information'] : null;
        $this->fonction   = (!empty($data['fonction'])) ? $data['fonction'] : null;
        $this->telephone   = (!empty($data['telephone'])) ? $data['telephone'] : null;
        $this->droit_mobile   = (!empty($data['droit_mobile'])) ? $data['droit_mobile'] : 1;
        
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
                'name'     => 'name',
                'required' => True,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 1024,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'firstname',
                'required' => True,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 1024,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'email',
                'required' => True,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'min'      => 0,
                            'max'      => 1048,
                        ),
                    ),
                    array(
                        'name' => 'EmailAddress'
                    )
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'societe',
                'required' => True,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 1024,
                        ),
                    )
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'profil_id',
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
            
            $inputFilter->add(array(
                'name'     => 'societe',
                'required' => True,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 248,
                        ),
                    ),
                ),
            ));
            

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}