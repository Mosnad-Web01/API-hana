<?php

use Core\LoggerUtility;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use model\User;


class AuthController {
    private $userModel;
    private  $secretKey;

    public function __construct($pdo) {
        $this->userModel= new User($pdo);

        $this->secretKey= 'secret';
    }
    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        LoggerUtility::logMessage('info', "Received data: " . json_encode($data) . " with status: ");

        $tokens = $this->userModel->authenticate($data['email'], $data['password']);

        if ($tokens) {
            jsonResponse(['accessToken' => $tokens['accessToken'], 'refreshToken' => $tokens['refreshToken']]);
        } else {
            jsonResponse(['message' => 'Invalid email or password.'], 401,'error');
        }
    }
//    public function logout(){
//     $data =json_decode(file_get_contents("php://input"), true);
//     $refreshToken = $data['refreshToken'] ?? '';
//     if($refreshToken){
//         $this->userModel->revokeRefreshToken($refreshToken);
//     }
//     jsonResponse(['message' => 'Successfully logged out.'], 204);
//    }

    public function refresh(){

        $data = json_decode(file_get_contents("php://input"), true);

        LoggerUtility::logMessage('info', "Received data: " . json_encode($data) . " with status: ");

        $refreshToken =$data['refreshToken'] ?? '';
     if (!$refreshToken) {
         jsonResponse(['message' => 'refresh token is required.'], 400);
     }
     try {
         $decoded = JWT::decode($refreshToken, new Key($this->secretKey,'HS256'));
         $userId = $decoded->data->id;
         $newAccessToken = $this->userModel->generatJwt('id', $userId);
         jsonResponse(['accessToken' => $newAccessToken]);
     }catch(ExpiredException $e) {
         jsonResponse(['message' =>'refresh token has expired'],401,'error');
     }catch (Exception $e){
         jsonResponse(['message'=>'Invalid refresh token'], 400,'error');
     }

//     $userId =$this->userModel->getUserRefreshToken($refreshToken);
//     if($userId){
//         $newAccessToken = $this->userModel->generatJwt(['id'=>$userId]);
//         jsonResponse(['token' => $newAccessToken]);
//
//     }else{
//         jsonResponse(['message' => 'Invalid refresh token.'], 401);
//     }
    }
}