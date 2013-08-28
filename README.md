ZF2 with Doctrine 2 ORM
=======================

Introduction
------------
This is an example Zend Framework 2 application using Doctrine 2 ORM. Most of this is based on [Marco Pivetta Blog](http://marco-pivetta.com/doctrine-orm-zf2-tutorial/) screencast.

Creation Steps
--------------

1.Create ZF2 project from skeleton using composer

```
curl -s https://getcomposer.org/installer | php --
php composer.phar create-project -sdev --repository-url="http://packages.zendframework.com" zendframework/skeleton-application zf2-example-doctrine2
```

2.Update composer.json to require Doctrine 2

```
php composer.phar self-update
php composer.phar require doctrine/doctrine-module:dev-master
php composer.phar require doctrine/doctrine-orm-module:dev-master
```

3.Install ZF Dev tools

```
php composer.phar require zendframework/zend-developer-tools:dev-master
```

4.Copy ZF Dev tools autoload config to application config and add modules

```
cp vendor/zendframework/zend-developer-tools/config/zenddevelopertools.local.php.dist config/autoload/zdt.local.php
```

Edit config/application.config.php:

```
...
'modules' => array(
    'ZendDeveloperTools',
    'DoctrineModule',
    'DoctrineORMModule',
    'Application',
),
...
```

5.Add first entity User

New file module/Application/src/Application/Entity/User.php:

```
<?php

namespace Application\Entity;
use Doctrine\ORM\Mapping as ORM;
/** @ORM\Entity */
class User {
    /**
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    * @ORM\Column(type="integer")
    */
    protected $id;

    /** @ORM\Column(type="string") */
    protected $fullName;

    public function getId()
    {
        return $this->id;
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function setFullName($value)
    {
        $this->fullName = $value;
    }
}
```

6.Add the Doctrine Driver to application config

Edit config/module.config.php:

```
return array(
    'doctrine' => array(
        'driver' => array(
            'application_entities' => array(
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Application/Entity')
            ),

            'orm_default' => array(
                'drivers' => array(
                    'Application\Entity' => 'application_entities'
                )
            )
        )
    ),
    ...
```

You should now see the new entity in the ZF2 Dev tool bar in the doctrine section at the bottom of the screen.

7.Add database config for Doctrine

New file local.php:

```
<?php

return array(
);
```

New file config/autoload/doctrine.local.php (for local MySql):

```
<?php

return array(
  'doctrine' => array(
    'connection' => array(
      'orm_default' => array(
        'driverClass' =>'Doctrine\DBAL\Driver\PDOMySql\Driver',
        'params' => array(
          'host'     => 'localhost',
          'port'     => '3306',
          'user'     => 'username',
          'password' => 'password',
          'dbname'   => 'database',
)))));
```

8.Validate the schema against the current DB (will fail since you haven't got any schema)

```
./vendor/bin/doctrine-module orm:validate-schema
```

9.Generate the schema

This will apply the ORM generated schema to the DB

```
./vendor/bin/doctrine-module orm:schema-tool:create
```

10.Update routes for CRUD actions

Edit module/Application/config/module.config.php:

```
...
'user' => array(
    'type'    => 'segment',
    'options' => array(
        'route'    => '/user[/][:action][/:id]',
        'constraints' => array(
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id'     => '[0-9]+',
        ),
        'defaults' => array(
            'controller' => 'Application\Controller\Index',
            'action'     => 'index',
        ),
    ),
),
...
```

11.Update IndexController for CRUD actions

Edit module/Application/src/Application/Controller/IndexController.php:

```
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
        $users = $this->getObjectManager()->getRepository('\Application\Entity\User')->findAll();

        return new ViewModel(array('users' => $users));
    }

    public function addAction()
    {
        if ($this->request->isPost()) {
            $user = new User();
            $user->setFullName($this->getRequest()->getPost('fullname'));

            $this->getObjectManager()->persist($user);
            $this->getObjectManager()->flush();
            $newId = $user->getId();

            return $this->redirect()->toRoute('home');
        }
        return new ViewModel();
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $user = $this->getObjectManager()->find('\Application\Entity\User', $id);

        if ($this->request->isPost()) {
            $user->setFullName($this->getRequest()->getPost('fullname'));

            $this->getObjectManager()->persist($user);
            $this->getObjectManager()->flush();

            return $this->redirect()->toRoute('home');
        }

        return new ViewModel(array('user' => $user));
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $user = $this->getObjectManager()->find('\Application\Entity\User', $id);

        if ($this->request->isPost()) {
            $this->getObjectManager()->remove($user);
            $this->getObjectManager()->flush();

            return $this->redirect()->toRoute('home');
        }

        return new ViewModel(array('user' => $user));
    }

    protected function getObjectManager()
    {
        if (!$this->_objectManager) {
            $this->_objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }

        return $this->_objectManager;
    }
}
```

12.Add views for CRUD actions

Edit module/Application/view/application/index/index.phtml:

```
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Users</h3>
            </div>
            <div class="panel-body">
                <a href="<?php echo $this->url('user', array('action'=>'add'));?>">Add User</a>

                <?php if (isset($users)) : ?>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Full name</th>
                        <th></th>
                    </tr>
                    </thead>
                <?php foreach($users as $user): ?>
                    <tbody>
                    <tr>
                        <td><?php echo $user->getId(); ?></td>
                        <td><?php echo $user->getFullName(); ?></td>
                        <td>
                            <a href="<?php echo $this->url('user', array('action'=>'edit', 'id' => $user->getId()));?>">Edit</a> |
                            <a href="<?php echo $this->url('user', array('action'=>'delete', 'id' => $user->getId()));?>">Delete</a>
                        </td>
                    </tr>
                    </tbody>
                <?php endforeach; ?>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

```

Edit module/Application/view/application/index/add.phtml:

```
<?php
// module/Album/view/album/album/add.phtml:

$title = 'Add new User';
$this->headTitle($title);
?>
<h1><?php echo $this->escapeHtml($title); ?></h1>
<form method="post">
  fullname: <input type="text" name="fullname"><br>
  <input type="submit" value="Submit">
</form>
```
Edit module/Application/view/application/index/edit.phtml:

```
<?php
// module/Album/view/album/album/add.phtml:

$title = 'Edit User';
$this->headTitle($title);
?>
<h1><?php echo $this->escapeHtml($title); ?></h1>
<form method="post">
  fullname: <input type="text" name="fullname" value="<?php echo $user->getFullname(); ?>"><br>
  <input type="submit" value="Submit">
</form>
```
Edit module/Application/view/application/index/delete.phtml:

```
<?php
// module/Album/view/album/album/add.phtml:

$title = 'Delete User';
$this->headTitle($title);
?>
<h1><?php echo $this->escapeHtml($title); ?></h1>
Are you sure you want to delete user <?php echo $user->getFullname(); ?>? <br/>
<form method="post">
  <input type="submit" value="Delete">
</form>
```

This covers basic CRUD actions using Doctrine 2 ORM in ZF2.

Links
-----
* [ZF2](http://framework.zend.com/)
* [Doctrine 2](http://www.doctrine-project.org/)
* [IBM blog](http://www.ibm.com/developerworks/library/os-doctrine-php-zend/)
* [Marco Pivetta Blog](http://marco-pivetta.com/doctrine-orm-zf2-tutorial/)