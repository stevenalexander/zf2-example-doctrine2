ZF2 with Doctrine 2 ORM
=======================

Introduction
------------
This is an example Zend Framework 2 application using Doctrine 2 ORM to implement entity versioning.

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

    // getters/setters
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

7.Add Database config for Doctrine

New file config/autoload/doctrine.local.php (for local MySql):

```
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

Links
-----
* [ZF2](http://framework.zend.com/)
* [Doctrine 2](http://www.doctrine-project.org/)
* [IBM blog](http://www.ibm.com/developerworks/library/os-doctrine-php-zend/)
* [Marco Pivetta Blog](http://marco-pivetta.com/doctrine-orm-zf2-tutorial/)