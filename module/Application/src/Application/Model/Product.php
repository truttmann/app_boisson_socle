<?php
 
namespace Application\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use \Zend\InputFilter\InputFilter;

class Product implements InputFilterAwareInterface
{
    public $id;
    public $libelle;
    public $description;
    public $image;
    public $prix_base;
    public $montant_taxe1;
    public $montant_taxe2;
    public $montant_tva;
    public $montant_total;
    public $producteur;
    public $contenance;
    public $type_embouteillage_id;
    public $type_colisage_id;
    public $published;
    
    protected $inputFilter;  

    public function exchangeArray($data)
    {
        $this->id          = (!empty($data['id'])) ? $data['id'] : null;
        $this->libelle         = (!empty($data['libelle'])) ? $data['libelle'] : null;
        $this->description          = (!empty($data['description'])) ? $data['description'] : null;
        $this->image         = (!empty($data['image'])) ? $data['image'] : null;
        $this->prix_base          = (!empty($data['prix_base'])) ? $data['prix_base'] : null;
        $this->montant_taxe1         = (!empty($data['montant_taxe1'])) ? $data['montant_taxe1'] : null;
        $this->montant_taxe2          = (!empty($data['montant_taxe2'])) ? $data['montant_taxe2'] : null;
        $this->montant_tva         = (!empty($data['montant_tva'])) ? $data['montant_tva'] : null;
        $this->montant_total          = (!empty($data['montant_total'])) ? $data['montant_total'] : null;
        $this->producteur         = (!empty($data['producteur'])) ? $data['producteur'] : null;
        $this->contenance          = (!empty($data['contenance'])) ? $data['contenance'] : null;
        $this->type_embouteillage_id         = (!empty($data['type_embouteillage_id'])) ? $data['type_embouteillage_id'] : null;
        $this->type_colisage_id          = (!empty($data['type_colisage_id'])) ? $data['type_colisage_id'] : null;
        $this->published          = (!empty($data['published'])) ? $data['published'] : null;
        
        $t = array_keys($data);
        foreach($t as $k) {
            if(in_array($k, array(
                "id",
                "libelle",
                "description",
                "image", 
                "prix_base",
                "montant_taxe1",
                "montant_taxe2",
                "montant_tva",
                "montant_total",
                "producteur",
                "contenance",
                "type_embouteillage_id",
                "type_colisage_id",
                "published")
            )) {
                continue;
            }
            $this->$k = $data[$k];
        }
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
                'name'     => 'prix_base',
                'required' => True,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Int',
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'montant_taxe1',
                'required' => False,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Int',
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'montant_taxe2',
                'required' => False,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Int',
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'montant_tva',
                'required' => True,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Int',
                    ),
                ),
            ));
            /*$inputFilter->add(array(
                'name'     => 'montant_total',
                'required' => True,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Int',
                    ),
                ),
            ));*/
            $inputFilter->add(array(
                'name'     => 'producteur',
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
                'name'     => 'contenance',
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
                'name'     => 'type_embouteillage_id',
                'required' => false,
                'allowEmpty' => true
            ));
            $inputFilter->add(array(
                'name'     => 'type_colisage_id',
                'required' => false,
                'allowEmpty' => true
            ));
            $inputFilter->add(array(
                'name'     => 'categorie_id',
                'required' => true,
                'allowEmpty' => False
            ));
            $inputFilter->add(array(
                'name'     => 'published',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}