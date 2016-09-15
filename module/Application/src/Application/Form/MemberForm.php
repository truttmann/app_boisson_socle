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

class MemberForm  extends Form
{
    public function __construct($params = array())
    {
         // we want to ignore the name passed
        parent::__construct('member');
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'attributes'=> array(
                'autocomplete' => 'off',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Nom',
            ),
        ));
        $this->add(array(
            'name' => 'firstname',
            'type' => 'Text',
            'attributes'=> array(
                'autocomplete' => 'off',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Prénom',
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'Text',
            'attributes'=> array(
                'autocomplete' => 'off',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));
        $this->add(array(
            'name' => 'societe',
            /*'type' => 'Text',
            'attributes'=> array(
                'autocomplete' => 'off',
                'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Société',
            ),*/
            'type' => 'Select',
            'attributes'=> array(
                'class' =>"form-control"
            ),
            'options' => array(
                'label' => 'Société',
                'value_options' => ((isset($params['societe']) && count($params['societe']) > 0)?$params['societe']:array()),
            )
        )); 
        $this->add(array(
            'name' => 'profil_id',
            'type' => 'Select',
            'attributes'=> array(
                'class' =>"form-control"
            ),
            'options' => array(
                'label' => 'Profil',
                'value_options' => ((isset($params['profil']) && count($params['profil']) > 0)?$params['profil']:array()),
            )
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