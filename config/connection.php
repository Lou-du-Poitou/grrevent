<?php
require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/../elements/error.exit.php';

function connection() {
    $db = null;

    $host = 'localhost';
    $charset = 'utf8mb4';
    $port = 3306;
    $dbname = 'projetweb';

    $username = 'root';
    $password = '';
    
    try {
        $dsn = "mysql:host=$host;charset=$charset;port=$port;dbname=$dbname;";
        $db = new PDO($dsn, $username, $password, [
            PDO::ATTR_PERSISTENT => true
        ]);
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return $db;
}
