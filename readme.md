## API Design

## Database Design

id | name | parent_store_id
------------ | ------------- | -------------
1 | A | 0
2 | A1 | 1
3 | A1a | 2
4 | A1b | 2
5 | A2 | 1

* A1, A2 will be children of A
* A1a, A1b will be children of A1
* A1a, A1b will be descendants of A

I originall start with soft delete approach but eventually discard that design, due to the descendant delete action.

## Service Architecture And Authentication

The whole architecture designed is based on the following data flow, which is referred to this article: 

[Microservice Authentication And Authorization Solutions](https://medium.com/tech-tajawal/microservice-authentication-and-authorization-solutions-e0e5e74b248a)

![Image of Auth](https://github.com/rickwxc/microservice-lumen-nodejs-docker/blob/master/docs/images/api.jpg)


