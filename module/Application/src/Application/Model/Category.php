<?php
 
namespace Application\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use \Zend\InputFilter\InputFilter;

class Category implements InputFilterAwareInterface
{
    public $id;
    public $libelle;    
    public $description;
    public $image;
    public $categorie_id;

    
    protected $inputFilter;  

    public function exchangeArray($data)
    {
        $this->id          = (!empty($data['id'])) ? $data['id'] : null;
        $this->libelle         = (!empty($data['libelle'])) ? $data['libelle'] : null;
        $this->description         = (!empty($data['description'])) ? $data['description'] : null;
        $this->image         = (!empty($data['image'])) ? $data['image'] : null;
        $this->categorie_id         = (!empty($data['categorie_id'])) ? $data['categorie_id'] : null;
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
                'name'     => 'libelle',
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
                'name'     => 'description',
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
                            'max'      => 2048,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'image',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'file/isImage', /*Attention, necessite l'extension fileinfo*/
                    ),
                    array(
                        'name'    => 'file/UploadFile',
                        'options' => array(
                            'target'    => './data/tmpuploads/avatar.png',
                            'randomize' => true,
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name'     => 'categorie_id',
                'required' => false,
                'allowEmpty' => true
            ));
            

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}