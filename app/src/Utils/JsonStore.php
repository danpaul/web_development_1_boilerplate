<?php

namespace App\Utils;

class JsonStore
{
    private string $filePath;
    private array $data;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->loadData();
    }

    private function loadData(): void
    {
        if (file_exists($this->filePath)) {
            $json = file_get_contents($this->filePath);
            $this->data = json_decode($json, true) ?? [];
        } else {
            $this->data = [];
        }
    }

    private function saveData(): void
    {
        $json = json_encode($this->data, JSON_PRETTY_PRINT);
        file_put_contents($this->filePath, $json);
    }

    public function getAll(): array
    {
        return $this->data;
    }

    public function getById($id): ?array
    {
        return $this->data[$id] ?? null;
    }

    public function create(array $item): int
    {
        $item["id"] = count($this->data) > 0 ? array_key_last($this->data) + 2 : 1;
        $this->data[] = $item;
        $this->saveData();
        return array_key_last($this->data);
    }

    public function update($id, array $item): bool
    {
        if (isset($this->data[$id])) {
            $this->data[$id] = $item;
            $this->saveData();
            return true;
        }
        return false;
    }

    public function delete($id): bool
    {
        if (isset($this->data[$id])) {
            unset($this->data[$id]);
            $this->data = array_values($this->data);
            $this->saveData();
            return true;
        }
        return false;
    }
}
