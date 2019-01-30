#curl --data "email=test@test.com&password=123456" -H "Content-Type: application/x-www-form-urlencoded" -XPOST http://localhost:8080/login

#call to the api gateway
curl --data "email=test@test.com&password=123456" -H "Content-Type: application/x-www-form-urlencoded" -H "Accept: text/plain" -XPOST http://localhost:8081/login

