<?php

namespace App\Controllers;

class GuestbookController
{
    public function getPosts($vars = [])
    {
        try {
            $connection_string = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
            $db = new \PDO(
                $connection_string,
                $_ENV['DB_USER'],
                $_ENV['DB_PASSWORD']
            );
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $stmt = $db->query('SELECT * FROM guestbook_2 ORDER BY posted_at DESC');
            $posts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            require_once __DIR__ . '/../Views/guestbook.php';
        } catch (\PDOException $e) {
            die('Database error: ' . $e->getMessage());
        }
    }
}