openssl genpkey -algorithm RSA -out auth_service_private_key.pem -pkeyopt rsa_keygen_bits:2048
openssl rsa -pubout -in auth_service_private_key.pem -out auth_service_public_key.pem
