<?php
require_once './elements/error.exit.php';

define('DB_ERROR_MESSAGE', 'Erreur base de données:');

function connection() {
    $db = null;
    
    try {
        $dsn = 'mysql:host=localhost;charset=utf8;port=3306;dbname=projetweb';
        $db = new PDO($dsn, 'root', '', [
            PDO::ATTR_PERSISTENT => true
        ]);
    } catch (PDOException $err) {
        errorExit(DB_ERROR_MESSAGE, $err);
    }

    return $db;
}
