For people who browse this repo.

This is only experimental for building micro services.

Basic tech stack:
1. Lumen for service implementation.
2. nodejs as API gateway (haven't clean up yet, just place in for demo)

Implemented:
1. use Fractal to unify json data 
2. create new user
3. login and to get JWT token
4. use JWT to protect private api calls
5. End-to-end testing for user apis
6. Localization for both message and test




for demo purpose, opaque token been created by sha1
opaque_token = crypto.createHash('sha1').update(jwt_token).digest('hex')


Some reference repos: 
docker original author: git@github.com:netojose/docker-lumen.git
api-gateway: git@github.com:ecojuntak/api-gateway.git 

