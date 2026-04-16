<?php
function connection() {
    try {
        $dsn = 'mysql:host=localhost;charset=utf8;port=3306;dbname=projetweb';
        $db = new PDO($dsn, 'root', '', [
            PDO::ATTR_PERSISTENT => true
        ]);
    } catch (PDOException $err) {
        echo '<pre>';
        var_dump($err);
        echo '</pre>';
        
        exit();
    }

    return $db;
}
