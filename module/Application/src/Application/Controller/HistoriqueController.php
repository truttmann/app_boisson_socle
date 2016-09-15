<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\User;
use Application\Model\Pointage;

class HistoriqueController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function listAction()
    {
        /* recuperation de la liste des societes */
        $view = new ViewModel();
        $temp = $this->getServiceLocator()->get("HistoriqueTable")->fetchAllBy(array(), "id", "desc");
        
        /* recuperation des objects */
        $res = array();
        foreach ($temp as $item){
            $item->user_id = $this->getServiceLocator()->get("UserTable")->fetchAll(array('id' => $item->user_id))->current();
            $res[] = $item;
        }
        $view->setVariable('histo', $res);
        return $view;
    }
}
