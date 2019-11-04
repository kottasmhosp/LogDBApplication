Ikentoo POS integration microservice (Symfony 4.2)
=============================

## Description

Integrate with Ikentoo POS system, making calls and allowing to
perform basic operations, that can be exposed to a frontend, allowing users to
view products add them to a cart/basket and order them. 

The microservice implements also a basket (cart) where the preferred products can be 
stored before sending the final order back to the Ikentoo POS system. 

The microservice acts as a proxy making calls to Ikentoo POS and returning simple JSON
responses and store necessary data in the DB

JWT (JSON Web Token) Authorization is used to access the API endpoints. 

Each hotel that integrates should have an account in order to access the exposed
API endpoints.

The software is deployed on Google Cloud App Engine and accessible
on the domain: https://ikentoo-integration.appspot.com

For details about the Ikentoo API please refer to 
https://ikentoo.github.io/apiDocs/

iKentoo REST APIs authentication done through an OAuth2 flow using the 
Authorisation Code grant type.
The token you will receive is non expiring so you will not need to 
bother with a refresh token. (defined as constant in the IkentooClient class)

## Installation

* Clone the repo
* Run `Composer install`
* Rename .env.demo for local use
* create db `php bin/console doctrine:database:create`

Generate the SSH keys:

`$ mkdir config/jwt
 $ openssl genrsa -out config/jwt/private.pem -aes256 4096
 $ openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem`
 
 In case first openssl command forces you to input password use following to get 
 the private key decrypted
 
 `$ openssl rsa -in config/jwt/private.pem -out config/jwt/private2.pem
  $ mv config/jwt/private.pem config/jwt/private.pem-back
  $ mv config/jwt/private2.pem config/jwt/private.pem`

## Implementation

For the JWT implementation check here: https://emirkarsiyakali.com/implementing-jwt-authentication-to-your-api-platform-application-885f014d3358

## Usage

First a hotel account/user needs to be created, by exposing the Register route, 
not exposed in production. After exposing the route create the account/user
(if not exists already for the specific hotel) making a curl request as follows:

`$ curl -X POST http://localhost:8000/register -d _username=johndoe -d _password=test -d _hotel=1` 

hotel is the hotel_id

Get a JWT token:

`$ curl -X POST -H "Content-Type: application/json" http://localhost:8000/login_check -d '{"username":"johndoe","password":"test"}'`

In order to access the exposed endpoints the returned token should be used as Bearer

Example call:

`curl -H "Authorization: Bearer [TOKEN]" http://localhost:8000/api`

The tocken expires after 3600 seconds (default) and a new authentication call
needs to be performed

Exposed endpoints:

Check for this the routes in **ApiEndpoints** controller


## Configure Database

If not exists, create a .env file using .env.demo as template

By default application use docker's database. If you want to change it change DATABASE_{variable}.

## Troubleshoot

If do not load check:
 - "var" directory exists in root path and is accessible by everyone
 - .env is created
