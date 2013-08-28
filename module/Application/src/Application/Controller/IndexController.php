<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\User;

class IndexController extends AbstractActionController
{
    protected $_objectManager;

    public function indexAction()
    {
        $user = new User();
        $user->setFullName('Simon Sample');

        $this->getObjectManager()->persist($user);
        $this->getObjectManager()->flush();

        die(var_dump($user->getId()));
    }

    protected function getObjectManager()
    {
        if (!$this->_objectManager) {
            $this->_objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }

        return $this->_objectManager;
    }
}
