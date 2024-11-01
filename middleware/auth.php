<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    private $secretKey;
    public function __construct(){
        $this->secretKey = "secret";
    }
    public function getUserFromJWT($jwt) {
        $decoded = $this->validateJWT($jwt);
        if ($decoded) {
            $user_id = $decoded->sub;
            return $user_id;
        }
         jsonResponse(['message' => 'Unauthorized'], 401,'error');
        return null;
    }

//if (in_array($jwt, [''])){
// jsonResponse(['You have been blocked'],404);
//}



    public function validateJWT($jwt){
        if (!$jwt){
            jsonResponse(['message' => 'Unauthorized'], 401,'error');
        }
        try{
            $decoded = JWT::decode($jwt,new Key($this->secretKey, 'HS256'));
            return $decoded;//Return decoded payload if valid
        } catch(ExpiredException $e){
            jsonResponse(['message' => 'Token has expired'], 401,'error');
            exit;//Exit after sending a response
        } catch(Exception $e){
            jsonResponse(['message' =>'Unautharized: Invalid token'], 401,'error');
            exit;//Exit after sending a response
        }
    }
}
