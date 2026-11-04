<?php

/*
 * ============================================================================
 *  WARNING: DO NOT COPY THIS STRUCTURE INTO YOUR FINAL PROJECT
 * ============================================================================
 *
 *  This controller deliberately violates MVC. File storage (reading and
 *  writing todos.json), request handling and redirects are all crammed into
 *  this one controller. There is no model or repository layer: the controller
 *  does its own data access.
 *
 *  This organization is NOT suitable for a student's final project. It exists
 *  only as a minimal, self-contained example.
 * ============================================================================
 */

namespace App\Controllers;

class TodoController
{
    private const DATA_DIR = __DIR__ . '/../../data';
    private const DATA_FILE = self::DATA_DIR . '/todos.json';

    public function index($vars = [])
    {
        $todos = $this->readTodos();
        require __DIR__ . '/../Views/todo.php';
    }

    public function add($vars = [])
    {
        $text = $_POST['text'] ?? '';
        if (!is_string($text)) {
            $text = '';
        }
        $text = trim($text);

        if ($text !== '') {
            $todos = $this->readTodos();
            array_unshift($todos, ['id' => bin2hex(random_bytes(8)), 'text' => $text]);
            $this->writeTodos($todos);
        }

        $this->redirect();
    }

    public function delete($vars = [])
    {
        $id = $_POST['id'] ?? '';
        if (!is_string($id)) {
            $id = '';
        }

        $todos = $this->readTodos();
        $remaining = array_values(array_filter(
            $todos,
            fn($todo) => ($todo['id'] ?? null) !== $id
        ));

        if (count($remaining) !== count($todos)) {
            $this->writeTodos($remaining);
        }

        $this->redirect();
    }

    private function ensureStorage(): void
    {
        if (!is_dir(self::DATA_DIR)) {
            mkdir(self::DATA_DIR, 0777, true);
            // umask reduces mkdir's mode, so set it explicitly
            chmod(self::DATA_DIR, 0777);
        }

        if (!file_exists(self::DATA_FILE)) {
            file_put_contents(self::DATA_FILE, '[]');
            chmod(self::DATA_FILE, 0666);
        }
    }

    private function readTodos(): array
    {
        $this->ensureStorage();
        $todos = json_decode(file_get_contents(self::DATA_FILE), true);

        // invalid JSON or anything that is not a list is treated as empty
        return is_array($todos) ? $todos : [];
    }

    private function writeTodos(array $todos): void
    {
        file_put_contents(
            self::DATA_FILE,
            json_encode(array_values($todos), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    private function redirect(): void
    {
        header('Location: /todo', true, 303);
        exit;
    }
}
