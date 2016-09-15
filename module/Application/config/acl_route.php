<?php

return array(
    "1" => array(
        'Application\Controller\User' => array(
            'index',
            'list',
            'add',
            'edit',
            'delete',
            'logout'
        ),
        'Application\Controller\Member' => array(
            'index',
            'list',
            'add',
            'edit',
            'delete',
            'logout'
        ),
        'Application\Controller\Catalogue' => array(
            'index',
            'listProduct',
            'addCategory',
            'addProduct',
            'editCategory',
            'editProduct',
            'delCategory',
            'delProduct'
        ),
        'Application\Controller\Index' => array(
            'index',
        ),
        'Application\Controller\Stock' => array(
            'index',
            'detailStock',
            'addProduct',
            'delProduct',
            'editProduct'
        ),
        'Application\Controller\Commande' => array(
            'index',
            'listCommande',             
            'addCommande',
            'editCommande',
            'delCommande',
        ),
        'Application\Controller\Historique' => array(
            'list',
        )
    ),
    "2" => array(
        'Application\Controller\User' => array(
            'index',
            'list',
            'add',
            'edit',
            'delete',
            'logout'
        )
    ) 
);
