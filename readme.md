## API Design

## Database Design

id | name | parent_store_id
------------ | ------------- | -------------
1 | A | 0
2 | A1 | 1
3 | A1a | 2
4 | A1b | 2
5 | A2 | 1

* A is the root store
* [A1, A2] is children of A
* [A1a, A1b] is children of A1
* [A1, A2, A1a, A1b] will be descendants of A
* /stores/1 gives A's content only, no branches information
* /stores/1?include=children gives A's content and also include children A1, A2 in branches field 
* /stores/1?include=descendant gives A's content and include all descendants A1, A2, A1a, A1b

I originally start with soft delete approach but eventually discard that design, due to the descendant recursive delete action.

## Service Architecture And Authentication

The whole architecture designed is based on the following data flow, which is referred to this article: 

[Microservice Authentication And Authorization Solutions](https://medium.com/tech-tajawal/microservice-authentication-and-authorization-solutions-e0e5e74b248a)

![Image of Auth](https://github.com/rickwxc/microservice-lumen-nodejs-docker/blob/master/docs/images/api.jpg)


