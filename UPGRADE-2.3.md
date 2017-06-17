UPGRADE FROM 2.2 to 2.3
=======================

- Class `Oro\Bundle\TaskBundle\EventListener\Datagrid\UserTaskGridListener`
    - changed the constructor signature: parameter `SecurityFacade $securityFacade` was replaced with `TokenAccessorInterface $tokenAccessor`
    - property `securityFacade` was replaced with `tokenAccessor`
