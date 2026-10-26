<?php
class Task
{
    private int $id;
    private string $title;
    private ?string $description;
    private ?string $dueDate;
    private string $priority;
    private string $status;
    private ?string $permission;
    private array $tags = [];

    public function __construct(int $id, string $title, ?string $description, ?string $dueDate, string $priority, string $status, ?string $permission)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->dueDate = $dueDate;
        $this->priority = $priority;
        $this->status = $status;
        if ($permission) {
            $this->permission = $permission;
        }
        $this->tags = TagRepository::getTagsByTaskId($id);
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function getDueDate(): ?string
    {
        return $this->dueDate;
    }
    public function getPriority(): string
    {
        return $this->priority;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getTags(): array
    {
        return $this->tags;
    }
    public function getPermission(): ?string
    {
        return $this->permission;
    }
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }
    public function getCollaborators(): array
    {
        return TaskRepository::getCollaboratorsByTaskId($this->id);
    }
}