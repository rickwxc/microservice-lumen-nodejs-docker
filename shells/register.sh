curl --data "name=tester&email=test@test.com&password=123456" -H "Content-Type: application/x-www-form-urlencoded" -XPOST http://localhost:8080/v1/users/register 
#curl  -d '{"name":"tester2","email":"test2@test.com","password":"123456"}'  -H "Content-Type: application/json" -X POST http://localhost:8080/v1/users/register 
