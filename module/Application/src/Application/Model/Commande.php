<?php
 
namespace Application\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use \Zend\InputFilter\InputFilter;

class Commande implements InputFilterAwareInterface
{
    public $id;
    public $numero;
	public $montant_ht;
	public $montant_taxe1;
	public $montant_taxe2;
	public $montant_tva;
	public $montant_ttc;
	public $created_at;
	public $updated_at;
    public $reception_at;
	public $created_by;
	public $created_for;
	public $validation_at;
	public $status;

    
    protected $inputFilter;  

    public function exchangeArray($data)
    {
        $this->id          = (!empty($data['id'])) ? $data['id'] : null;
        $this->numero          = (!empty($data['numero'])) ? $data['numero'] : null;
        $this->montant_ht          = (!empty($data['montant_ht'])) ? $data['montant_ht'] : null;
        $this->montant_taxe1          = (!empty($data['montant_taxe1'])) ? $data['montant_taxe1'] : null;
        $this->montant_taxe2          = (!empty($data['montant_taxe2'])) ? $data['montant_taxe2'] : null;
        $this->montant_tva          = (!empty($data['montant_tva'])) ? $data['montant_tva'] : null;
        $this->montant_ttc          = (!empty($data['montant_ttc'])) ? $data['montant_ttc'] : null;
        $this->created_at          = (!empty($data['created_at'])) ? $data['created_at'] : null;
        $this->updated_at          = (!empty($data['updated_at'])) ? $data['updated_at'] : null;
        $this->reception_at          = (!empty($data['reception_at'])) ? $data['reception_at'] : null;
        $this->created_by          = (!empty($data['created_by'])) ? $data['created_by'] : null;
        $this->created_for          = (!empty($data['created_for'])) ? $data['created_for'] : null;
        $this->validation_at          = (!empty($data['validation_at'])) ? $data['validation_at'] : null;
        $this->status          = (!empty($data['status'])) ? $data['status'] : null;
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