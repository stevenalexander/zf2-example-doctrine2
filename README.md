ZF2 with Doctrine 2 ORM for Entity versioning
=======================

Introduction
------------
This is an example Zend Framework 2 application using Doctrine 2 ORM to implement entity versioning.

Creation Steps
--------------

1. Create ZF2 project from skeleton using composer

    curl -s https://getcomposer.org/installer | php --
    php composer.phar create-project -sdev --repository-url="http://packages.zendframework.com" zendframework/skeleton-application zf2-example-doctrine2


2. Update composer.json to require Doctrine 2

    php composer.phar self-update
    php composer.phar require doctrine/doctrine-module:dev-master
    php composer.phar require doctrine/doctrine-orm-module:dev-master

Links
-----
* [ZF2](http://framework.zend.com/)
* [Doctrine 2](http://www.doctrine-project.org/)
* [IBM blog](http://www.ibm.com/developerworks/library/os-doctrine-php-zend/)
* [Marco Pivetta Blog](http://marco-pivetta.com/doctrine-orm-zf2-tutorial/)