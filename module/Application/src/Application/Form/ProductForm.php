<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form;

 // Add these import statements
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Form\Form;

class ProductForm  extends Form
{
    public function __construct($params = array())
    {
         // we want to ignore the name passed
        parent::__construct('product');
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'libelle',
            'type' => 'Text',
            'attributes'=> array(
                'autocomplete' => 'off',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Libellé',
            ),
        ));
        $this->add(array(
            'name' => 'published',
            'type' => 'Radio',
            'attributes'=> array(
                'class' => 'radio_custom'
            ),
            'options' => array(
                'label' => 'Statut',
                'label_attributes' => array(
                    'class'  => 'radio-control'
                ),
                'value_options' => array(
                    '0' => 'Inactif',
                    '1' => 'Actif',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'description',
            'type' => 'Text',
            'attributes'=> array(
                'autocomplete' => 'off',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Description',
            ),
        ));
        $this->add(array(
            'name' => 'image',
            'type' => 'File',
            'attributes'=> array(
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Image',
            ),
        ));
        $this->add(array(
            'name' => 'prix_base',
            'type' => 'Text',
            'attributes'=> array(
                'autocomplete' => 'off',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Base de prix',
            ),
        ));
        $this->add(array(
            'name' => 'montant_taxe1',
            'type' => 'Text',
            'attributes'=> array(
                'autocomplete' => 'off',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Taxe n°1',
            ),
        ));
        $this->add(array(
            'name' => 'montant_taxe2',
            'type' => 'Text',
            'attributes'=> array(
                'autocomplete' => 'off',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Taxe n°2',
            ),
        ));
        $this->add(array(
            'name' => 'montant_tva',
            'type' => 'Text',
            'attributes'=> array(
                'autocomplete' => 'off',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'TVA',
            ),
        ));
        /*$this->add(array(
            'name' => 'montant_total',
            'type' => 'Text',
            'attributes'=> array(
                'autocomplete' => 'off',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Total',
            ),
        ));*/
        $this->add(array(
            'name' => 'producteur',
            'type' => 'Text',
            'attributes'=> array(
                'autocomplete' => 'off',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Producteur',
            ),
        ));
        $this->add(array(
            'name' => 'contenance',
            'type' => 'Text',
            'attributes'=> array(
                'autocomplete' => 'off',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Contenance',
            ),
        ));
        $this->add(array(
            'name' => 'type_embouteillage_id',
            'type' => 'Application\Form\Select',
            'attributes'=> array(
                'class' =>"form-control",
            ),
            'options' => array(
                'label' => 'Type d\'embouteillage',
                /*'empty_option' => 'Choisir',*/
                'value_options' => ((isset($params['type_embouteillage']) && count($params['type_embouteillage']) > 0)?$params['type_embouteillage']:array()),
            )
        ));
        $this->add(array(
            'name' => 'type_colisage_id',
            'type' => 'Application\Form\Select',
            'attributes'=> array(
                'class' =>"form-control",
            ),
            'options' => array(
                'label' => 'Type colisage',
                /*'disable_inarray_validator' => true,*/
                'value_options' => ((isset($params['type_colisage']) && count($params['type_colisage']) > 0)?$params['type_colisage']:array()),
            )
        ));
        $this->add(array(
            'name' => 'categorie_id',
            'type' => 'Application\Form\Select',
            'attributes'=> array(
                'class' =>"form-control",
                'multiple' => 'multiple',
            ),
            'options' => array(
                'label' => 'Catégorie',
                /*'empty_option' => 'Choisir',*/
                'value_options' => ((isset($params['categorie']) && count($params['categorie']) > 0)?$params['categorie']:array()),
            )
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Valider',
                'id' => 'submitbutton',
                'class' => 'btn btn-info btn-sm'
            ),
        ));
    }
}