<?php
class User
{
    private int $id;
    private string $username;
    private string $email;
    private string $rol;
    private array $tasks;

    public function __construct(int $id, string $username, string $email, string $rol)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->rol = $rol;
        $this->tasks = TaskRepository::getTasksByUserId($id);
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getRol(): string
    {
        return $this->rol;
    }
    public function getTasks(): array
    {
        $this->tasks = TaskRepository::getTasksByUserId($this->id);
        return $this->tasks;
    }
}