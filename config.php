<?php
return [
    'host' => 'localhost',
    'dbname'=> 'todos',
    'port' => '3306',
    'charset' => 'utf8'
];



//<?php
//use PDO;
//
//const DB_HOST = 'localhost';
//const DB_NAME = 'todos';
//const DB_USER = 'root';
//const DB_PASS = '';
//
//const DB_PORT = '3306';
//
//
//function getDBConnection() {
//
//
//    try {
//        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME .";port=" .DB_PORT, DB_USER, DB_PASS);
//        echo 'PDO is working!';
//
//        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
//        return $pdo;
//    } catch (PDOException $e) {
//        jsonResponse(['message' => 'Database connection failed: ' . $e->getMessage()], 500);
//    }
//}
//

