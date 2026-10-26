<?php
class TaskRepository
{
    public static function getTasksByUserId(int $userId): array
    {
        $db = DB::connect();
        $q = "SELECT * FROM tasks WHERE user_id=$userId";
        $result = $db->query($q);
        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = new Task($row['id'], $row['title'], $row['description'], $row['due_date'], $row['priority'], $row['status']);
        }
        return $tasks;
    }

    public static function getTaskById(int $id): Task
    {
        $db = DB::connect();
        $q = "SELECT * FROM tasks WHERE id=$id";
        $result = $db->query($q);
        $row = $result->fetch_assoc();
        return new Task($row['id'], $row['title'], $row['description'], $row['due_date'], $row['priority'], $row['status']);
    }
}