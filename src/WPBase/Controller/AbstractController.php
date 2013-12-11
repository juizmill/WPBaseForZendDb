<?php
namespace WPBase\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;


class AbstractController extends AbstractActionController
{
    protected $entity;
    protected $controller;
    protected $route;
    protected $service;
    protected $form;
    protected $dirMessages;

    public function __construct()
    {
        $this->dirMessages = __DIR__.'/../../../menssages.phtml';
    }

    public function indexAction()
    {
        $page = (int) $this->params()->fromRoute('page', 0);

        $data = $this->getEntity()->fetchAllPaginator($page, 10);

        $menssages = new ViewModel();
        $menssages->setTemplate($this->dirMessages);

        $viewModel = new ViewModel();
        $viewModel->addChild($menssages, 'menssages');

        if ($this->flashMessenger()->hasInfoMessages())
            $menssages->setVariables(array('data' => $data, 'info' => $this->flashMessenger()->getInfoMessages()));

        if ($this->flashMessenger()->hasSuccessMessages())
            $menssages->setVariables(array('data' => $data, 'success' => $this->flashMessenger()->getSuccessMessages()));

        if ($this->flashMessenger()->hasErrorMessages())
            $menssages->setVariables(array('data' => $data, 'error' => $this->flashMessenger()->getErrorMessages()));

        return $viewModel->setVariables(array('data' => $data));
    }

    public function novoAction()
    {
        $menssages = new ViewModel();
        $menssages->setTemplate($this->dirMessages);

        $viewModel = new ViewModel();
        $viewModel->addChild($menssages, 'menssages');


        /**
         * @var $form \Zend\Form\Form
         */
        if (is_string($this->form))
            $form = new $this->form;
        else
            $form =  $this->form;

        $request = $this->getRequest();

        if ($request->isPost()) {

            $form->setData($request->getPost());

            if ($form->isValid()) {

                $service = $this->getServiceLocator()->get($this->service);

                if ($service->save($request->getPost()->toArray())) {
                    $this->flashMessenger()->addSuccessMessage('Cadastrado com sucesso!');
                } else {
                    $this->flashMessenger()->addErrorMessage('Não foi possivel cadastrar! Tente mais tarde');
                }

                return $this->redirect()->toRoute($this->route, array('controller' => $this->controller, 'action' => 'novo'));
            }

        }

        if ($this->flashMessenger()->hasSuccessMessages()) {
            $menssages->setVariables(array('success' => $this->flashMessenger()->getSuccessMessages()));
            return $viewModel->setVariables(array('form' => $form));
        }

        if ($this->flashMessenger()->hasErrorMessages()) {
            $menssages->setVariables(array('error' => $this->flashMessenger()->getErrorMessages()));
            return $viewModel->setVariables(array('form' => $form));
        }

        $this->flashMessenger()->clearMessages();

        return $viewModel->setVariables(array('form' => $form));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function editarAction()
    {
        /**
         * @var $form \Zend\Form\Form
         */

        if (is_string($this->form))
            $form = new $this->form;
        else
            $form =  $this->form;

        $request = $this->getRequest();
        $param = (int) $this->params()->fromRoute('id', 0);

        $repository = $this->getEm()->getRepository($this->entity);
        $entity = $repository->find($param);

        if ($entity) {

            $menssages = new ViewModel();
            $menssages->setTemplate($this->dirMessages);

            $viewModel = new ViewModel();
            $viewModel->addChild($menssages, 'menssages');


            $array = array();
            foreach($entity->toArray() as $key => $value){
                if ($value instanceof \DateTime)
                    $array[$key] = $value->format('d/m/Y');
                else
                    $array[$key] = $value;
            }

            $form->setData($array);

            if ( $request->isPost() ) {

                //Converte oque vem por POST para ARRAY
                $data = $request->getPost()->toArray();

                $form->setData($data);

                if ( $form->isValid() ) {

                    $service = $this->getServiceLocator()->get($this->service);
                    $data['id'] = (int) $param;

                    if($service->save($data)){
                        $this->flashMessenger()->addSuccessMessage('Atualizado com sucesso!');
                    }else{
                        $this->flashMessenger()->addErrorMessage('Não foi possivel atualizar! Tente mais tarde');
                    }

                    return $this->redirect()->toRoute($this->route, array('controller' => $this->controller, 'action' => 'editar', 'id' => $param));
                }
            }

        } else {

            $this->flashMessenger()->addInfoMessage('Registro não foi encontrado!');

            return $this->redirect()->toRoute($this->route, array('controller' => $this->controller));
        }


        if ($this->flashMessenger()->hasSuccessMessages()) {
            $menssages->setVariables(array('success' => $this->flashMessenger()->getSuccessMessages()));
            return $viewModel->setVariables(array('form' => $form, 'id' => $param));
        }

        if ($this->flashMessenger()->hasErrorMessages()) {
            $menssages->setVariables(array('error' => $this->flashMessenger()->getErrorMessages()));
            return $viewModel->setVariables(array('form' => $form, 'id' => $param));
        }

        if ($this->flashMessenger()->hasInfoMessages()) {
            $menssages->setVariables(array('warning' => $this->flashMessenger()->getInfoMessages()));
            return $viewModel->setVariables(array('form' => $form, 'id' => $param));
        }

        $this->flashMessenger()->clearMessages();

        return $viewModel->setVariables(array('form' => $form, 'id' => $param));

    }

    /**
     * @return \Zend\Http\Response
     */
    public function removerAction()
    {
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        $service = $this->getServiceLocator()->get($this->service);

        $id = (int) $this->params()->fromRoute('id', 0);

        if ($request->isXmlHttpRequest()){
            if( $service->remove(array('id' => $id)) )
                return new JsonModel(array('success' => 'Registro deletado com sucesso!'));
            else
                return new JsonModel(array('error' => 'Não foi possivel deletar o registro! Atualize a pagina e tente novamente.'));
        }

        //Redireciona para a pagina principal
        return $this->redirect()->toRoute($this->route, array('controller' => $this->controller));
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
