<?php
namespace WPBase\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class AbstractController extends AbstractActionController
{
    protected $entity;

    public function indexAction()
    {
        $page = (int) $this->params()->fromRoute('page', 0);

        $data = $this->getEntity()->fetchAllPaginator($page, 10);

        return new ViewModel(array('data' => $data));
    }

    public function novoAction()
    {
        return new ViewModel();
    }

    public function editarAction()
    {
        return new ViewModel();
    }

    public function removerAction()
    {
        return new ViewModel();
    }


    public function getEntity()
    {
        if (!$this->entity) {
            $sm = $this->getServiceLocator();
            $this->entity = $sm->get('WPEntity');
        }
        return $this->entity;
    }
}
