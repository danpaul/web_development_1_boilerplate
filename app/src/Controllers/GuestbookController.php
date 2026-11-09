<?php

namespace App\Controllers;

class GuestbookController
{
    public function getPosts($vars = [])
    {
        $connection_string = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
        $db = $this->getConnection();
        $stmt = $db->query('SELECT * FROM posts ORDER BY posted_at DESC');
        $posts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../Views/guestbook.php';
    }

    public function addPost(){
        $db = $this->getConnection();
        $statement = $db->prepare('INSERT INTO posts (name, email, message) VALUES (:name, :email, :message)');
        $statement->bindParam(':name', $_POST['name']);
        $statement->bindParam(':email', $_POST['email']);
        $statement->bindParam(':message', $_POST['message']);
        $statement->execute();
        $this->getPosts();
    }

    private function getConnection(){
        try {
            $connection_string = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
            $db = new \PDO(
                $connection_string,
                $_ENV['DB_USER'],
                $_ENV['DB_PASSWORD']
            );
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $db;
        } catch (\PDOException $e) {
            die('Database error: ' . $e->getMessage());
        }
    }
}