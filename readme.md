## Repo Structure

### [store service source](https://github.com/rickwxc/microservice-lumen-nodejs-docker/blob/master/www/stores-and-branches/)
stores and branches api

### [auth service source](https://github.com/rickwxc/microservice-lumen-nodejs-docker/blob/master/www/auth/)
* register new user
* login to get api_key

### [api gateway source](https://github.com/rickwxc/microservice-lumen-nodejs-docker/blob/master/api-gateway/)
* dispatch request

## API Design
For quick glance:

![Image of stores](https://github.com/rickwxc/microservice-lumen-nodejs-docker/blob/master/docs/images/stores-shot.png)

* create a store branch
  - POST /v1/stores
* update a store branch
  - PUT /v1/stores/1
* delete a store branch along with all of its children
  - DELETE /v1/stores/1
* move a store branch (along with all of its children) to a different store branch
  - POST /v1/stores/1/branches
    * with parameter branchStoreId
* view all store branches with all of their children
  - GET /v1/stores
* view one specific store branch with all of its children
  - GET /v1/stores/1?include=children
  - GET /v1/stores/1?include=descendant
* view one specific store branch without any children
  - GET /v1/stores/1

Here is the [API document in swagger format](https://github.com/rickwxc/microservice-lumen-nodejs-docker/blob/master/docs/swaggers/stores/stores.yaml)


## Service Architecture And Authentication


The whole architecture designed is based on the following data flow, which is referred to this article: 

[Microservice Authentication And Authorization Solutions](https://medium.com/tech-tajawal/microservice-authentication-and-authorization-solutions-e0e5e74b248a)
Here is the annotated graph:
![Image of Auth](https://github.com/rickwxc/microservice-lumen-nodejs-docker/blob/master/docs/images/api.jpg)

## System setup on docker 
* 2 image for php running on auth service and store service
* 1 image for mysql running auth, store databases, including test databases
* 1 image for nodejs for Api gateway
* 1 image for MongoDB for store mapping between api_key and Jwt Token
* 1 image for nginx

Due to time limits, I haven't fully automate everything yet, some process need some manually 
work:

* create databases
* nodejs server config

