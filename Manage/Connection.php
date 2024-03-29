<?php

class DbManager
{
    public static function Connect($dbname)
    {
        $dsn = "mysql:dbname={$dbname};host=localhost";
        try {
            $pdo = new PDO($dsn, 'michele_rizzo', 'observers.kilns.handhelds.');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $exception) {
            die("connection al DB Fallita: " . $exception->getMessage());
        }
    }
}