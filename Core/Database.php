<?php

namespace Core;

use PDO;
use Core\LoggerUtility;

class Database
{
    private $connection;

    public function __construct($config, $username = 'root', $password = '')
    {

        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};port={$config['port']};charset={$config['charset']}";
        $this->connection = new PDO($dsn, $username, $password, [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    }


    public function query($query, $params = [])
    {
        $error = null;
        try {
        $statement = $this->connection->prepare($query);
        $statement->execute($params);
        //logging
            LoggerUtility::logMessage('debug', "Query executed successfully: {$query} with params: " . json_encode($params));

        } catch (PDOException $e) {
            $error = $e->getMessage();
            //logging
            LoggerUtility::logMessage('error', "Query failed: {$query} with params: " . json_encode($params) . ". Error: {$error}");
            return $error;
        }


        return $statement;
    }
}