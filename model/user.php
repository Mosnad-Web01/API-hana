<?php
namespace model;

use Core\LoggerUtility;
use Firebase\JWT\JWT;

class User
{
    //private $pdo;
    private $secretKey;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->secretKey = 'secret';
    }
    public function register($data)
    {
        if (is_object($data)) {

            $data = json_decode(json_encode($data), true);

            LoggerUtility::logMessage('info', "Received data: " . json_encode($data) . " with status: ");

        }

        return $this->pdo->query("INSERT INTO users (email, password) VALUES (:email, :password)",
            ['email' => $data['email'], 'password' => $data['password']]);
    }
    public function authenticate($email, $password)
    {

        $user = $this->pdo->query("SELECT * FROM users WHERE email = :email ", ["email" => $email])
            ->fetch();

        //check if user exists and password is correct
        if (($user) && password_verify($password, $user['password'])) {

            $refreshToken = $this->generateRefreshToken($user['id']);
            $accessToken = $this->generatJwt($user);
            return ['accessToken' => $accessToken, 'refreshToken' => $refreshToken];
        }
        return null;
    }

    public function generatJwt($user, $expires = 3600)
    {
        $payload = [
            'iss' => 'localhost',
            'sub' => $user['id'],
            'iat' => time(),
            'exp' => time() + ($expires),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    private function generateRefreshToken($userId)
    {
        //$refreshToken = bin2hex(random_bytes(24));
        $refreshToken = $this->generatJwt(['id' => $userId], 86400);
//        $this->pdo->query("UPDATE users SET refreshToken = :refreshToken WHERE id = :id",[
//            "refreshToken"=>$refreshToken,
//            "id"=>$userId
//        ]);

        return $refreshToken;

    }
//    public function revokeRefreshToken($refreshToken,$userId){
//        return $this->pdo->query("UPDATE users SET refreshToken = NULL WHERE refreshToken = :refreshToken"
//        ,["refreshToken"=>$refreshToken, "id"=>$userId]);
//
//
//    }
//public function getUserRefreshToken($refreshToken){
//        return $this->pdo->query("SELECT id FROM users WHERE refreshToken = :refreshToken"
//        ,["refreshToken"=>$refreshToken])->fetchColumn();
//}
}
