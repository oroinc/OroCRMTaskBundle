UPGRADE FROM 1.10 to 2.0 
========================

####OroTaskBundle:
- Removed fields `workflowItem` and `workflowStep` from entity `Oro\Bundle\TaskBundle\Entity\Task`
- Removed fields `workflowItem` and `workflowStep` from entity `Oro\Bundle\TaskBundle\Entity\TaskSoap`

####SOAP API was removed
- removed all dependencies to the `besimple/soap-bundle` bundle. 
- removed classes:   
    - Oro\Bundle\TaskBundle\Controller\Api\Soap\TaskController
    - Oro\Bundle\TaskBundle\Tests\Functional\Controller\Api\Soap\TaskControllerTest
