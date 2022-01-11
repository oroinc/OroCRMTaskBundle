# Oro\Bundle\TaskBundle\Entity\Task

## ACTIONS  

### get

Retrieve a specific task record.

{@inheritdoc}

### get_list

Retrieve a collection of task records.

{@inheritdoc}

### create

Create a new task record.

The created record is returned in the response.

{@inheritdoc}

{@request:json_api}
Example:

```JSON
{
   "data": {
      "type": "tasks",
      "attributes": {
         "subject": "Lorem ipsum dolor sit amet, consectetuer adipiscing elit",
         "description": "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.",
         "dueDate": "2017-02-16T22:36:37Z"
      },
      "relationships": {
         "taskPriority": {
            "data": {
               "type": "taskpriorities",
               "id": "normal"
            }
         },
         "status": {
            "data": {
               "type": "taskstatuses",
               "id": "open"
            }
         },
         "activityTargets": {
            "data": [
               {
                  "type": "contacts",
                  "id": "61"
               },
               {
                  "type": "accounts",
                  "id": "45"
               }
            ]
         }
      }
   }
}
```
{@/request}

### update

Edit a specific task record.

The updated record is returned in the response.

{@inheritdoc}

{@request:json_api}
Example:

```JSON
{
   "data": {
      "type": "tasks",
      "id": "1",
      "attributes": {
         "subject": "Lorem ipsum dolor sit amet, consectetuer adipiscing elit",
         "description": "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.",
         "dueDate": "2017-02-16T22:36:37Z"
      },
      "relationships": {
         "taskPriority": {
            "data": {
               "type": "taskpriorities",
               "id": "normal"
            }
         },
         "status": {
            "data": {
               "type": "taskstatuses",
               "id": "open"
            }
         },
         "activityTargets": {
            "data": [
               {
                  "type": "contacts",
                  "id": "61"
               },
               {
                  "type": "accounts",
                  "id": "45"
               }
            ]
         }
      }
   }
}
```
{@/request}

### delete

Delete a specific task record.

{@inheritdoc}

### delete_list

Delete a task records.

{@inheritdoc}

## FIELDS

### status

#### create

{@inheritdoc}

**The required field.**

### taskPriority

#### create

{@inheritdoc}

**The required field.**

### subject

#### create

{@inheritdoc}

**The required field.**

#### update

{@inheritdoc}

**This field must not be empty, if it is passed.**

## SUBRESOURCES

### owner

#### get_subresource

Retrieve the record of the user who is the owner of a specific task record.

#### get_relationship

Retrieve the ID of the user who is the owner of a specific task record.

#### update_relationship

Replace the owner of a specific task record.

{@request:json_api}
Example:

```JSON
{
  "data": {
    "type": "users",
    "id": "37"
  }
}
```
{@/request}

### organization

#### get_subresource

Retrieve the record of the organization that a specific task belongs to.

#### get_relationship

Retrieve the ID of the organization that a specific task record belongs to.

#### update_relationship

Replace the organization that a specific task record belongs to.

{@request:json_api}
Example:

```JSON
{
  "data": {
    "type": "organizations",
    "id": "1"
  }
}
```
{@/request}

### status

#### get_subresource

Retrieve status records configured for a specific task record.

#### get_relationship

Retrieve the ID of the status record configured for a specific task record.

#### update_relationship

Replace the status record configured for a specific task record.

{@request:json_api}
Example:

```JSON
{
  "data": {
    "type": "taskstatuses",
    "id": "open"
  }
}
```
{@/request}

### taskPriority

#### get_subresource

Retrieve task priority records configured for a specific task record.

#### get_relationship

Retrieve the ID of the task priority records configured for a specific task record.

#### update_relationship

Replace the task priority configured for a specific task record.

{@request:json_api}
Example:

```JSON
{
  "data": {
    "type": "taskpriorities",
    "id": "normal"
  }
}
```
{@/request}

### createdBy

#### get_subresource

Retrieve user that created specific task record.

#### get_relationship

Retrieve ID of the user that created specific task record.


# Oro\Bundle\TaskBundle\Entity\TaskPriority

## ACTIONS

### get

Retrieve the collection of task priority records.

{@inheritdoc}

### get_list

Retrieve a specific task priority record.

{@inheritdoc}


# Extend\Entity\EV_Task_Status

## ACTIONS

### get

Retrieve a specific task status record.

Task status is the state the task is in (Open, In Progress, Closed).

### get_list

Retrieve a collection of task statuses.

Task status is the state the task is in (Open, In Progress, Closed).
