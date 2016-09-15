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

class CategoryForm  extends Form
{
    public function __construct($params = array())
    {
         // we want to ignore the name passed
        parent::__construct('category');
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
            'name' => 'categorie_id',
            'type' => 'Application\Form\Select',
            'attributes'=> array(
                'class' =>"form-control",
            ),
            'options' => array(
                'label' => 'Parent',
                'empty_option' => 'Choisir',
                'disable_inarray_validator' => true,
                'value_options' => ((isset($params['category']) && count($params['category']) > 0)?$params['category']:array()),
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