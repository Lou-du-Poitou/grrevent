<?php
require_once './config/constants.php';
require_once './elements/error.exit.php';

function connection() {
    $db = null;

    $host = 'localhost';
    $charset = 'utf8mb4';
    $port = 3306;
    $dbname = 'projetweb';
    
    try {
        $dsn = "mysql:host=$host;charset=$charset;port=$port;dbname=$dbname;";
        $db = new PDO($dsn, 'root', '', [
            PDO::ATTR_PERSISTENT => true
        ]);
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return $db;
}
