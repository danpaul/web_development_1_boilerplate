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
    private const TODOS_FILE = self::DATA_DIR . '/todos.json';
    private const COMPLETED_FILE = self::DATA_DIR . '/completed.json';

    public function index($vars = [])
    {
        $todos = $this->readList(self::TODOS_FILE);
        $completed = $this->readList(self::COMPLETED_FILE);
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
            $todos = $this->readList(self::TODOS_FILE);
            array_unshift($todos, ['id' => bin2hex(random_bytes(8)), 'text' => $text]);
            $this->writeList(self::TODOS_FILE, $todos);
        }

        $this->redirect();
    }

    public function delete($vars = [])
    {
        $id = $_POST['id'] ?? '';
        if (!is_string($id)) {
            $id = '';
        }

        $todos = $this->readList(self::TODOS_FILE);
        $remaining = array_values(array_filter(
            $todos,
            fn($todo) => ($todo['id'] ?? null) !== $id
        ));

        if (count($remaining) !== count($todos)) {
            $this->writeList(self::TODOS_FILE, $remaining);
        }

        $this->redirect();
    }

    public function complete($vars = [])
    {
        $id = $_POST['id'] ?? '';
        if (!is_string($id)) {
            $id = '';
        }

        $todos = $this->readList(self::TODOS_FILE);
        foreach ($todos as $index => $todo) {
            if (($todo['id'] ?? null) === $id) {
                $completed = $this->readList(self::COMPLETED_FILE);
                // same {id, text} object, newest first
                array_unshift($completed, $todo);
                unset($todos[$index]);
                $this->writeList(self::COMPLETED_FILE, $completed);
                $this->writeList(self::TODOS_FILE, $todos);
                break;
            }
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

        foreach ([self::TODOS_FILE, self::COMPLETED_FILE] as $file) {
            if (!file_exists($file)) {
                file_put_contents($file, '[]');
                chmod($file, 0666);
            }
        }
    }

    private function readList(string $file): array
    {
        $this->ensureStorage();
        $items = json_decode(file_get_contents($file), true);

        // invalid JSON or anything that is not a list is treated as empty
        return is_array($items) ? $items : [];
    }

    private function writeList(string $file, array $items): void
    {
        file_put_contents(
            $file,
            json_encode(array_values($items), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    private function redirect(): void
    {
        header('Location: /todo', true, 303);
        exit;
    }
}
