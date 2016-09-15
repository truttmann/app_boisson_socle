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

class StockForm  extends Form
{
    public function __construct($params = array())
    {
         // we want to ignore the name passed
        parent::__construct('stock');
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'produit_id',
            'type' => 'Application\Form\Select',
            'attributes'=> array(
                'class' =>"form-control",
            ),
            'options' => array(
                'label' => 'Produit',
                /*'empty_option' => 'Choisir',*/
                'value_options' => ((isset($params['product']) && count($params['product']) > 0)?$params['product']:array()),
            )
        ));
        $this->add(array(
            'name' => 'quantite',
            'type' => 'Text',
            'attributes'=> array(
                'autocomplete' => 'off',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Quantité',
            ),
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